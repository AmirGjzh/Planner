# Docker Production Setup

> Reference documentation. Read this before touching anything under `docker/`,
> `laravel/compose.production.yml`, `.dockerignore`, or the `.env.*` templates. Shared
> conventions (secrets, healthchecks, wrappers, the seeding decision) live in
> `docker/shared-conventions.md`.

## Purpose and Audience

Describes the production deployment: a single Linux host on a private LAN, plain HTTP, no
domain. nginx + php-fpm + MySQL + Redis via Docker Compose. Not user documentation.

## Current Status (snapshot)

| Item | Value |
|---|---|
| Deployment model | Single host, LAN, plain HTTP, no domain, personal/small usage |
| Orchestration | Docker Compose v2 (`laravel/compose.production.yml`), project `planner` |
| php-fpm image | `php:8.4-fpm`, multi-stage (see Dockerfile), runs as `www-data` |
| nginx image | `nginx:1.30`, serves `/var/www/public` + proxies PHP |
| MySQL / Redis | `mysql:8.4` (own volume) / `redis:8.10` (ephemeral) |
| Build-only | `composer:2.10`, `node:24` |
| Containers | `planner-php-fpm`, `planner-nginx`, `planner-mysql`, `planner-redis` |
| Volumes | `planner_database`, `planner_storage` |
| Host ports | only `80:80` (nginx) |
| Restart / logging | `unless-stopped`; `json-file` capped 10m × 3 |

## Target Deployment Model

- All services on one Docker host; nothing exposed to the public internet.
- No HTTPS/CDN/domain — TLS deliberately deferred (see Explicitly Deferred).
- The repo is the artifact: fresh machine = clone + filled `.env` + `up --build`, then it
  runs forever. **No bind mounts** — code lives inside the images (immutable), so updating
  the app always means rebuilding.

## Architecture

```
        Browser (LAN)
             |  http://<host>:80
             v
   planner-nginx          serves /var/www/public (built assets), proxies PHP → fpm:9000
             |  fastcgi_pass php-fpm:9000
             v
   planner-php-fpm        runs the app; entrypoint runs setup each boot, then php-fpm
        |  mysql:8.4 (data in volume)     | redis:8.10 (sessions/cache, ephemeral)
        v                                  v
   planner-mysql                        planner-redis
```

- Container names are DNS (`mysql`, `redis`, `php-fpm`, `nginx` upstream). Only nginx
  publishes a port.
- nginx serves assets from its own baked `public/build`; php-fpm renders `@vite` from its own
  copy. **Assets are built twice** (once per image) with identical pins/lockfiles so the
  output matches.
- Boot chain (healthchecks): mysql + redis → php-fpm → nginx.

## Repository Layout (Docker Parts)

```
/
├── planner / planner-dev           command wrappers
├── .dockerignore                   build-context filter (with comments)
├── docker/production/
│   ├── php-fpm/Dockerfile + entrypoint.sh
│   └── nginx/Dockerfile + conf.d/default.conf
├── docker/development/              dev image (see development.md)
└── laravel/
    ├── compose.production.yml      prod stack definition
    ├── .env.production             committed prod template (source of truth)
    ├── .env                        real values, git-ignored
    ├── bootstrap/app.php           app-level HTTP config (trusted proxies, redirects)
    ├── config/database.php         redis connections (default/cache/session)
    └── routes/web.php + app/Providers/AppServiceProvider.php   Route blocking
```

## File-by-File

### `laravel/compose.production.yml`

Single-file Compose v2 spec.

- **php-fpm**: built with `target: production`; `env_file: .env` (full environment);
  `APP_KEY: ${APP_KEY:?Set APP_KEY in .env}` guard; `storage:/var/www/storage` volume;
  healthcheck `php -r 'exit(@fsockopen(...9000) ? 0 : 1)'`.
- **nginx**: built from the nginx Dockerfile; `80:80`; healthcheck `curl -fs
  http://127.0.0.1/up`; depends on healthy php-fpm.
- **mysql**: `MYSQL_ROOT_PASSWORD`/`DB_PASSWORD` required via `${VAR:?}`, DB/user from
  `DB_DATABASE`/`DB_USERNAME`; real-auth healthcheck (`mysql -e 'SELECT 1'`, never
  `mysqladmin ping`) with `start_period: 30s`; data in the `database` volume.
- **redis**: `requirepass` via `REDIS_PASSWORD`, healthcheck `redis-cli ping` via
  `REDISCLI_AUTH`, no volume (ephemeral — losing sessions/cache just forces a re-login).
- All secrets enforced via `${VAR:?msg}` — Compose aborts naming the missing one. Never
  `MYSQL_PWD` (breaks first-boot init; see shared-conventions).
- Explicit `name: planner` keeps volume prefix stable regardless of checkout directory.
- Logging capped (the app is chatty: `LOG_LEVEL=info`, access logs) so unbounded Docker logs
  can't fill the disk.

### `docker/production/php-fpm/Dockerfile`

Multi-stage image. Stages:

1. `composer:2.10 AS composer` — pinned Composer binary (also reused by nginx's build).
2. `php:8.4-fpm AS builder` — compiles extensions (`pdo_mysql intl mbstring zip bcmath gd` +
   pecl `redis`), layer-cached `composer install --no-dev --no-scripts
   --optimize-autoloader` (only `composer.*` copied first, so app edits don't bust the
   dependency layer), then the full source + `package:discover`. Discarded after artifacts
   are extracted.
3. `node:24 AS frontend` — `npm ci --no-audit --no-fund`, copies `vendor/` from builder (so
   Vite can resolve the Livewire ESM import), `npm run build`.
4. `php:8.4-fpm AS production` — re-installs the `-dev` system packages (their runtime names
   are not stable across the tag's base Debian releases — bookworm/trixie shifts like
   `libzip4`→`libzip5`), copies compiled `.so` files + `conf.d/*.ini` + `docker-php-ext-*`
   helpers, `mv php.ini-production php.ini` (no custom ini — base image already loads opcache
   with sane defaults), copies app + `public/build`, `chown` to `www-data`, `USER www-data`.

**No composer binary in the runtime image** (build-time only), verified intent.

### `docker/production/nginx/Dockerfile`

`composer:2.10` + `node:24` stages build `vendor/` and assets; runtime is
`nginx:1.30` with the vhost, built `public/` tree, and `curl` (apt — used only by the
healthcheck; the Debian nginx image has no `wget`).

### `docker/production/nginx/conf.d/default.conf`

Single vhost. Facts worth knowing:

- `listen 80`, `server_name _`, `root /var/www/public`, `server_tokens off` (no version leak).
- `client_max_body_size 10m` — above nginx's 1 MB default so modest uploads don't get an
  early 413 (PHP's own upload limits still apply).
- gzip on (level 6, min 1024) for text/JSON/JS/CSS/SVG/fonts — Vite output is not
  precompressed.
- Security headers (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`,
  `Permissions-Policy`) re-declared on `location /build` — a location-level `add_header`
  replaces server-level inheritance entirely.
- `location /` → SPA fallback `try_files ... /index.php?$query_string`.
- `location /build` → immutable one-year cache (`Cache-Control: public,
  max-age=31536000, immutable`), access logs off.
- `error_page 404 /index.php` → Laravel renders fancy 404s.
- Fastcgi to `php-fpm:9000` with `SCRIPT_FILENAME $realpath_root$fastcgi_script_name`,
  sets `X-Forwarded-For`/`X-Forwarded-Proto` (the app trusts these), `fastcgi_hide_header
  X-Powered-By`.
- `location ~ /\.(?!well-known).*` → `deny all` (a stray `.env` in `public/` is never served).

### `docker/production/php-fpm/entrypoint.sh`

Entrypoint: validates `APP_KEY` (`base64:` prefix, decodes to exactly 32 bytes for
AES-256-CBC), then `php artisan migrate --seed --force` and `php artisan optimize`, then
`exec php-fpm` (pid 1 = php-fpm; signals work).

- The key guard catches the common mistake of pasting a bare `openssl rand -base64 32`
  output without the `base64:` prefix. It only reports the problem — fix is on the host.
- **Seeds on every boot** — see the seeding decision in `shared-conventions.md`. `UserSeeder` is a
  no-op today, but this line must be revisited before that ever creates real users
  (a known-credential dev seed must not reach production).
- `migrate --force` is idempotent; `optimize` bakes config/routes/views, which is why
  "edit `.env`, recreate php-fpm" is sufficient for env changes.

### `.dockerignore`

Build-context filter (with comments; source of truth in the file). Effects:

- **Never** ships `.env` / `.env.*` into any image — secrets arrive only via `env_file`.
- `.ai/` (this skill tree), `tests/`, `scripts/` stay in the repo but never touch a
  container.
- The writable-dir marker `.gitignore` files under `storage/framework/*` ARE included — the
  image ships the storage skeleton so the named volume initializes with `www-data` ownership.

### `laravel/.env.production`

Committed template, source of truth. Prod values baked in; only the secrets
blank. Highlights:

- `APP_ENV=production`, `APP_LOCALE=fa`, `APP_THEME=ocean`, `APP_DEBUG=false`,
  `APP_URL=http://localhost`.
- `SESSION_DRIVER=redis` on the dedicated `session` connection (DB 2, added in
  `config/database.php`), `SESSION_ENCRYPT=true`, `SESSION_EXPIRE_ON_CLOSE=false` (persistent
  cookie; the 120-minute server-side lifetime stays the real limit), `SESSION_SECURE_COOKIE`
  unset (plain HTTP).
- `CACHE_STORE=redis`; `REDIS_CACHE_LOCK_CONNECTION=cache`.
- `LOG_LEVEL=info`, `BCRYPT_ROUNDS=12` (explicit default).

After the mysql volume is initialized, `MYSQL_DATABASE/USER/PASSWORD` are no longer re-read —
keep them in sync with the actual DB user.

### APP-SIDE changes for this deployment

These ship with the app; `docker/` alone is not enough.

- **`bootstrap/app.php`**: `trustProxies(at: '*')` (only nginx in front, which sets
  `X-Forwarded-*` for every request), `redirectGuestsTo('/login')`,
  `redirectUsersTo('/dashboard')`.
- **`config/database.php`**: a third redis connection `session` (DB 2) — key-space isolation
  from cache (DB 1) and default (DB 0) on the single Redis instance.
- **Route blocking** in `routes/web.php` + `AppServiceProvider::boot()`: every route and the
  Livewire update endpoint carry `->block(10, 10)` (10s timeout, 10s lock) — overlapping
  duplicate requests for the same session wait instead of double-writing. The Livewire update
  route is redeclared with the same `block` because the default one is registered by
  Livewire itself. Uses the cache-lock redis connection. Exists because sessions moved to a
  durable store where stale concurrent writes are a real risk.
- **`.gitignore` / `composer.json` / `storage/framework/views/.gitignore`**: un-ignores
  `laravel/.env.production` (committed) and replaces the blanket `/storage/framework` ignore
  with per-directory `.gitignore` files — keeps the storage skeleton in git → in the image →
  the named volume auto-populates with `www-data` ownership.

## Environment Variables: Host → Container

| Compose value | Host `.env` key | Purpose |
|---|---|---|
| `env_file: .env` (php-fpm) | (all) | full app env (`DB_*`, `REDIS_*`, `SESSION_*`, `APP_KEY`, ...) |
| mysql `MYSQL_*` | `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD:?`, `MYSQL_ROOT_PASSWORD:?` | DB/user creation (first init only) |
| redis `REDIS_PASSWORD` + `$$REDIS_PASSWORD` in command | `REDIS_PASSWORD:?` | `requirepass` |
| `REDISCLI_AUTH` | `REDIS_PASSWORD:?` | healthcheck auth |
| `${VAR:?msg}` guards | `APP_KEY`, `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD` | required — Compose aborts if empty |

`$$` escapes a literal `$` from Compose interpolation so the container expands it at runtime
(redis command); plain `${VAR}` is interpolated at parse time (mysql healthcheck). No
`MYSQL_PWD` anywhere (see shared-conventions).

## Decisions Log

Chronological; the durable "why" for each significant choice. Details are inline in the
file-by-file sections above; this is the one-line record.

1. **Multi-stage images, zero bind mounts** — the repo is the artifact; a fresh machine just
   builds. Rejected bind-mounting code: host paths/perms/deps would leak in, nothing to hand
   over.
2. **Build assets in BOTH images** — php-fpm renders `@vite`, nginx serves files. Rejected a
   shared-asset builder volume: ordering hacks; duplicating with identical pins is simpler.
3. **Pinned MAJOR.MINOR tags** (`composer:2.10`, `php:8.4-fpm`, `node:24`, `nginx:1.30`,
   `mysql:8.4`, `redis:8.10`) — patch updates come free per rebuild; minor moves are
   deliberate acts.
4. **No custom php.ini / pool conf** — base image ships production-sane defaults incl.
   opcache (verified loaded).
5. **Redis for sessions+cache, MySQL for data** — Redis runs without a volume (loss = forced
   re-login, acceptable).
6. **`phpredis` client** (PHP extension) — the config default.
7. **`SESSION_ENCRYPT=true`**, **`SESSION_EXPIRE_ON_CLOSE=false`** (persistent cookie; the
   skeleton's `true` made no sense for this app).
8. **`Route::block()` + Livewire update-route redeclaration** — serialize overlapping
   requests once sessions live in Redis.
9. **`trustProxies('*')`** — behind our only proxy (nginx sets `X-Forwarded-*`). Rejected a
   proxy IP list: the host's Docker bridge IP varies.
10. **Entrypoint guards `APP_KEY`, runs `migrate --seed --force` + `optimize` on every boot
    ** — see shared-conventions for the seeding caveat.
11. **Dedicated redis `session` connection (DB 2)** — key-space isolation from cache/default.
12. **Single named network, only nginx publishes a port** — MySQL/Redis unreachable by
    accident.
13. **Named volumes `database` + `storage`**; `bootstrap/cache` deliberately NOT a volume
    (regenerated by `optimize` each boot).
14. **Log rotation 10m × 3 everywhere**, **`restart: unless-stopped` everywhere**,
    **healthcheck chain** mysql/redis → php-fpm → nginx + `/up`.
15. **Static container names + `server_name _`** — predictable, small-scale, not swarm.
16. **Small hardening**: `server_tokens off`, security headers, dotfile-deny, `/build`
    immutable cache, `client_max_body_size` above nginx's 1 MB default.
17. **`laravel/.env.production` committed; `.env` ignored.**
18. **`unzip` in the builder** (`--prefer-dist` + PECL tarballs); `curl` removed from the
    builder apt (leftover from the old composer curl-installer).
19. **Secrets enforced at compose level** `${VAR:?msg}` + entrypoint `APP_KEY` format guard —
    replaced old `:-root`/`:-` fallbacks that silently defaulted to weak/empty values.
20. **`start_period: 30s` on mysql** — first boot init is slow; without it mysql flips
    "unhealthy" during init.
21. **Production stage keeps `-dev` libs** — their names are stable across the tag's base
    Debian release changes; the runtime names are not. Slimming isn't worth build fragility.
22. **`npm ci --no-audit --no-fund`** — reproducible from lockfiles, quiet, fast.
23. **All-Debian, no-suffix base images** (`node:24`, `nginx:1.30`, `redis:8.10` were
    `-alpine`) — one distro everywhere for free (mysql has no Alpine variant anyway). Costs
    size only. The Debian nginx image ships no `wget`, so nginx installs `curl` for its
    healthcheck.
24. **Healthchecks real, not just "alive"** — mysql `SELECT 1` (not `mysqladmin ping` which
    passes on failed auth), nginx `curl -fs /up`, redis `ping` via `REDISCLI_AUTH`, php-fpm
    TCP 9000; all exec-form, no shell.
25. **Layer-cached `composer install`** — only `composer.json`/`composer.lock` precede the
    install so app-code edits don't invalidate dependency layers; `package:discover` runs
    after the full source copy.

## Deployment Runbook (fresh machine, golden path)

Prerequisites: Docker Engine with Compose v2 (Docker ≥ 24) and git.

```bash
git clone <repo-url> planner && cd planner
cp laravel/.env.production laravel/.env
```

Fill the secrets in `laravel/.env`:

```bash
APP_KEY=$(printf 'base64:%s' "$(openssl rand -base64 32)")   # never reuse from elsewhere
# also set: DB_PASSWORD, MYSQL_ROOT_PASSWORD, REDIS_PASSWORD
```

Then:

```bash
./planner up            # builds on first run (or the raw compose form)
./planner ps
curl -s http://localhost/up        # expect 200 (Laravel health route)
```

First run needs internet once (base images + composer/npm downloads); later runs reuse the
Docker layer cache. Verify stack health (`ps` all healthy) and logs (`logs --tail=50
php-fpm`, no ERRORS, migration ran). If the migration failed the container restarts and `/up`
won't answer.

## Day-to-Day Operations

| Task | Command |
|---|---|
| Status / logs | `./planner ps` / `./planner logs [service]` |
| Apply app changes (after `git pull`) | `up -d --build` |
| Apply only env change | edit `.env` → `up -d` (entrypoint re-bakes config via `optimize`) |
| Force recreate everything | `up -d --force-recreate` |
| Stop everything | `down` (volumes survive) |
| **Never** | `down -v` / `docker volume rm planner_database planner_storage` — that's the whole database |
| Fresh APP_KEY | on your machine: `printf 'base64:%s\n' "$(openssl rand -base64 32)"`, then `.env`, recreate php-fpm |
| Shell into a service | `exec php-fpm sh` (runs as `www-data`) |
| Manual DB backup (no automation yet) | `exec mysql sh -c 'mysqldump -u planner -p"$MYSQL_PASSWORD" planner' > backup.sql` |

APP_KEY rotation (rare; forces re-login): generate, edit `.env` → `up -d --force-recreate
php-fpm nginx`, verify `/up` and a fresh login.

### Env-change workflow (why it works)

Compose re-reads `.env` on every command, but a running container freezes its env at start.
So: change `.env` → recreate the container that needs it. php-fpm's entrypoint runs
`optimize` on boot, so cached config is rebuilt automatically (no manual `config:clear`).

## Verification & Troubleshooting

| Symptom | Check | Fix |
|---|---|---|
| `up` aborts "required variable … missing a value" | `.env` has a blank secret | fill the named var |
| "Unsupported cipher or incorrect key length" | `APP_KEY` missing the `base64:` prefix | regenerate + `up -d --force-recreate php-fpm` |
| `502 Bad Gateway` | is php-fpm healthy? | `up -d php-fpm`; check `logs php-fpm` |
| `/up` 500 / "whoops" | php-fpm logs | app exception; check memory, APP_KEY validity |
| Container restart-loop | `logs` shows migration error | mysql not ready (wait; `start_period` covers first boot) or DB creds wrong |
| MySQL won't accept new creds | volume already initialized | fix the DB user instead (or recreate volume deliberately) |
| Redis NOAUTH errors | `.env` `REDIS_PASSWORD` vs container env | edit `.env`, recreate redis + php-fpm |
| Everything healthy but login fails | session store | `exec redis redis-cli -a "$REDIS_PASSWORD" FLUSHALL` (nukes sessions+cache; data intact) |
| Port 80 busy on host | `ss -ltnp` | stop the other process, or map another port in compose |

Healthcheck definitions (for diagnosis):

| Service | Test | interval / timeout / retries / start |
|---|---|---|
| php-fpm | `php -r` fsockopen TCP 9000 | 10s / 3s / 5 / 30s |
| nginx | `curl -fs http://127.0.0.1/up` (full stack) | 30s / 3s / 3 / 5s |
| mysql | `mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -e SELECT 1` (exec CMD, real auth) | 10s / 5s / 5 / 30s |
| redis | `redis-cli ping` (auth via `REDISCLI_AUTH`) | 10s / 5s / 5 |

`depends_on: condition: service_healthy` orders them: mysql+redis → php-fpm → nginx.

## Docker Hygiene on This Host

Expected state: project images plus build bases pulled on demand, the `database` and
`storage` volumes, no dangling images. Rules forever:

- **Never** `docker system prune -a --volumes`, `docker volume prune`, or `down -v` on a live
  install — those delete `planner_database` / `planner_storage`.
- Safe weekly-ish hygiene: `docker system prune -f` (dangling images) and `docker builder
  prune -f`.
- Build bases are ephemeral — deleting them is fine; the next `up --build` re-pulls.

## Keeping This Setup Updated

Structural truth: **the images are immutable**; the repo defines the deployment. Updates are
"change pins/files, rebuild, verify."

- App/config changes → `git pull && up -d --build && curl -s http://localhost/up` + smoke
  login. Both images rebuild from the same context/locks (no version skew).
- Dependency bumps (`composer.lock` / `package-lock.json`): bump in dev, commit
  both lockfiles, rebuild. Never hand-edit locks in prod; never `up --build` dirty.
- Framework upgrade: treat like a dependency bump but re-check `bootstrap/app.php` /
  `config/database.php` against the new skeleton and re-run the config-defaults audit.
- Base-image patch updates: free via rebuild (all tags are MAJOR.MINOR). Minor updates:
  deliberate — bump in both Dockerfiles / compose, rebuild, full verification, update the
  version table and decisions log.
- Env surface changes: add to `laravel/.env.production` + a new decisions-log row; live
  installs need the same manual edit + recreate flow.
- Adding infrastructure (queue worker, scheduler, scale-out): copy the established pattern
  (composer stage + worker image, `depends_on`/health, logging/restart model, named volume +
  persistence *before* any Redis-backed queue — Redis has no volume today). Out of current
  scope unless a feature requires it.
- Editing `docker/`: keep `.dockerignore`, `.env.*` templates, both Dockerfiles and
  `default.conf` coherent across the two images; rebuild + health-verify; update this doc.
  The comment-free style in the images is intentional — the "why" lives here.
- Composer skew guard: both images must keep using the same `composer:2.10` base.

Anti-patterns: bind-mounting code "just for host edits"; running the dev stack inside the
prod network; editing `laravel/.env.production` and assuming live installs see it (they only
see their `.env`); `up -d --build` on a dirty tree.

## Explicitly Deferred (and Not Done)

Recorded so future-me knows these were conscious decisions, not oversights:

- **TLS/HTTPS**: no domain, LAN-local, plain HTTP. When TLS appears: nginx gains `listen 443`
  + certs + `443:443` in compose, and `SESSION_SECURE_COOKIE=true` (or `null` behind a
  trusted TLS proxy).
- **Automated backups**: none; the manual one-liner is in Day-to-Day Operations (personal LAN
  app, small data).
- **Multi-instance scaling / load balancing / monitoring**: single host; healthchecks are the
  only oversight.
- **Rate limiting beyond Laravel's defaults** (the `block()` work is about write races and
  session lock, not abuse).
- **CDN / compression pre-bake / Redis persistence**: unnecessary or contrary to size.
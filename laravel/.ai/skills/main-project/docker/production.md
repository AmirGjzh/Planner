# Docker Production Setup

> Reference documentation. Read this before touching anything under `docker/`,
> `laravel/compose.production.yml`, `.dockerignore`, or the `.env.*` templates. Shared
> conventions (secrets, healthchecks, wrappers, the seeding decision) live in
> `docker/shared-conventions.md`.

## Purpose and Audience

Describes the production deployment: a single Linux host on a private LAN, plain HTTP, no
domain. nginx + php-fpm + a queue worker + MariaDB + Redis via Docker Compose. Not user
documentation.

## Current Status (snapshot)

| Item | Value |
|---|---|
| Deployment model | Single host, LAN, plain HTTP, no domain, personal/small usage |
| Orchestration | Docker Compose v2 (`laravel/compose.production.yml`), project `planner` |
| php-fpm image | `php:8.4-fpm`, multi-stage (see Dockerfile), runs as `www-data`; queue shares this exact image (`planner-app:latest`) |
| nginx image | `nginx:1.30-alpine`, serves `/var/www/public` + proxies PHP; `curl` via apk for the healthcheck |
| MariaDB / Redis | `mariadb:12.3` (own volume) / `redis:8.10-alpine` (appendonly volume) |
| Build-only | `node:24-slim` (frontend stage); Composer = pinned phar 2.10.3 + sha256 in the builder (no donor image) |
| Dockerfiles | one file, `docker/production/Dockerfile`, two final targets (`production`, `nginx`) |
| Containers | `planner-php-fpm`, `planner-queue`, `planner-nginx`, `planner-mariadb`, `planner-redis`, `planner-mailpit` |
| Volumes | `planner_database`, `planner_storage`, `planner_redis` |
| Host ports | `80:80` (nginx), `8025:8025` (Mailpit UI) |
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
   planner-queue          same image; entrypoint, then queue:work (consumes redis jobs)
        |  mariadb:12.3 (data in volume)  | redis:8.10-alpine (sessions/cache/jobs, appendonly)
        v                                 v
   planner-mariadb                        planner-redis        planner-mailpit (SMTP :1025, UI :8025)
```

- Container names are DNS (`mariadb`, `redis`, `php-fpm`, `nginx` upstream). nginx and the
  Mailpit UI publish ports; MariaDB/Redis/SMTP do not.
- nginx serves assets from its own baked `public/build`; php-fpm renders `@vite` from its own
  copy. Both copies come from **one shared vite build** (the `frontend` stage) — single
  install graph, no skew possible.
- Boot chain (healthchecks): mariadb + redis → php-fpm → nginx + queue. Mailpit is
  deliberately outside the chain — mail capture must never gate the app.

## Repository Layout (Docker Parts)

```
/
├── planner / planner-dev           command wrappers
├── .dockerignore                   build-context filter (with comments)
├── docker/production/
│   ├── Dockerfile                   multi-stage: builder → frontend → production + nginx
│   ├── entrypoint.sh
│   └── nginx/conf.d/default.conf
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

- **php-fpm**: `image: planner-app:latest`, built from the shared Dockerfile with
  `target: production`; `env_file: .env` (full environment);
  `APP_KEY: ${APP_KEY:?Set APP_KEY in .env}` guard; `storage:/var/www/storage` volume;
  healthcheck `php -r 'exit(@fsockopen(...9000) ? 0 : 1)'`.
- **queue**: `image: planner-app:latest` with **no build section** — it runs the exact
  image php-fpm built (one image, zero duplicate build); `command: php artisan queue:work
  --tries=3 --sleep=1`; `SKIP_MIGRATE=1` so its entrypoint skips the migrate+seed php-fpm
  already ran (it starts only after php-fpm is healthy; `optimize` still runs). Same
  `env_file`/`APP_KEY` guard/`storage` volume/logging as
  php-fpm; `depends_on: php-fpm` healthy + `mailpit` started. No port
  → no healthcheck (mailpit precedent).
- **nginx**: built from the same Dockerfile (`target: nginx`); `80:80`; healthcheck `curl -fs
  http://127.0.0.1/up`; depends on healthy php-fpm.
- **mariadb**: `MARIADB_ROOT_PASSWORD`/`DB_PASSWORD` required via `${VAR:?}`, DB/user from
  `DB_DATABASE`/`DB_USERNAME`; real-auth healthcheck (`mariadb -e 'SELECT 1'`, never
  `mariadb-admin ping`) with `start_period: 30s`; data in the `database` volume.
- **redis**: `requirepass` via `REDIS_PASSWORD`, healthcheck `redis-cli ping` via
  `REDISCLI_AUTH`; `--appendonly yes` with a `redis:/data` volume — sessions/cache/jobs
  survive restarts (the queue needs it).
- **mailpit**: `axllent/mailpit:v1.27`, publishes only the web UI (`8025:8025`); SMTP 1025
  stays internal (`mailpit:1025` from php-fpm). No compose healthcheck — the image ships a
  built-in exec-form `HEALTHCHECK ["/mailpit", "readyz"]`. Referenced by php-fpm/queue
  `depends_on` as `service_started` (boot ordering only — decisions row 38); no volume
  (captured mail is disposable test data).
- All secrets enforced via `${VAR:?msg}` — Compose aborts naming the missing one. Never
  `MARIADB_PWD` (breaks first-boot init; see shared-conventions).
- Explicit `name: planner` keeps volume prefix stable regardless of checkout directory.
- Logging capped (the app is chatty: `LOG_LEVEL=info`, access logs) so unbounded Docker logs
  can't fill the disk.

### `docker/production/Dockerfile`

One multi-stage file serving both final images — **one composer install, one npm ci, one
vite build per clean build** (BuildKit shares the common stages across the two target
builds). Final targets:

- **`target: production`** → `planner-app:latest`, run by both `php-fpm` and `queue`.
- **`target: nginx`** → static assets + fastcgi proxy.

Stages:

1. `php:8.4-fpm AS builder` — compiles extensions (`pdo_mysql intl mbstring zip pcntl` + pecl
   `redis`, pinned `redis-6.3.0`; `pcntl` drains the queue worker on SIGTERM — decisions row
   36; no `gd`/`bcmath` — zero call sites, same evidence as dev),
   installs the pinned Composer phar 2.10.3 with sha256 verification (`curl`/`ca-certificates`
   ship in the base image — no donor stage, no apt), layer-cached `composer install --no-dev
   --no-scripts --optimize-autoloader` (only `composer.*` copied first, so app edits don't
   bust the dependency layer), then the full source + `package:discover`. Discarded after
   artifacts are extracted.
2. `node:24-slim AS frontend` — `npm ci --no-audit --no-fund`, copies `vendor/` from builder (so
   Vite can resolve the Livewire ESM import in `resources/js/app.js`), `npm run build`. Every
   npm dependency is pure JS or ships a prebuilt linux-x64 glibc binary — nothing compiles, so
   slim needs no toolchain. Both final images take their assets from this single build — no
   skew possible.
3. `php:8.4-fpm AS production` — installs the **runtime** libs only (`libicu76 libonig5
   libzip5`, ~55MB lighter than the `-dev` packages; names pinned to the base's Debian release,
   loud `apt-get` failure on a release move — see decisions log), copies compiled `.so` files +
   `conf.d/*.ini` + `docker-php-ext-*` helpers, `mv php.ini-production php.ini` (no custom ini
   — base image already loads opcache with sane defaults), copies the app with
   `COPY --chown=www-data` + a one-directory `chown` for `/var/www` itself
   (no `chown -R` — decisions row 37), `public/build`
   root-owned (the app only reads the manifest), `USER www-data`.
4. `nginx:1.30-alpine AS nginx` — the vhost, `curl` via apk (healthcheck only; keeps the
   `curl -fs /up` semantics busybox wget lacks), and `public/` copied from the shared
   `frontend` stage. No composer/npm/vite here anymore — previously this file was a second,
   duplicate build graph.

**No composer binary in the runtime image** (build-time only), verified intent.

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

### `docker/production/entrypoint.sh`

Entrypoint: validates `APP_KEY` (`base64:` prefix, decodes to exactly 32 bytes for
AES-256-CBC), then `php artisan migrate --seed --force` **unless `SKIP_MIGRATE=1`**, then
`php artisan optimize`, then
`exec "$@"` — the image `CMD` decides the process (`php-fpm` for the web service, the
`queue` service overrides it with `queue:work`); pid 1 receives it, signals work.

- The key guard catches the common mistake of pasting a bare `openssl rand -base64 32`
  output without the `base64:` prefix. It only reports the problem — fix is on the host.
- **`SKIP_MIGRATE=1` is set only on the queue service**: php-fpm migrates + seeds on its own
  boot and the queue starts only after php-fpm is healthy, so the worker skips the redundant
  run. php-fpm itself never sets it — the web tier remains the boot-time migrator.
- **Seeds on every php-fpm boot** — see the seeding decision in `shared-conventions.md`. `UserSeeder` is a
  no-op today, but this line must be revisited before that ever creates real users
  (a known-credential dev seed must not reach production).
- `migrate --force` is idempotent; `optimize` bakes config/routes/views, which is why
  "edit `.env`, recreate php-fpm + queue" is sufficient for env changes.

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
  `APP_URL=http://localhost` — the reset-email link is rooted here, so keep this as
  the URL you actually open the app with (`localhost` is right while Mailpit is read
  on the server itself; a deployment changes this one line).
- `SESSION_DRIVER=redis` on the dedicated `session` connection (DB 2, added in
  `config/database.php`), `SESSION_ENCRYPT=true`, `SESSION_EXPIRE_ON_CLOSE=false` (persistent
  cookie; the 120-minute server-side lifetime stays the real limit), `SESSION_SECURE_COOKIE`
  unset (plain HTTP).
- `CACHE_STORE=redis`; `REDIS_CACHE_LOCK_CONNECTION=cache`.
- `QUEUE_CONNECTION=redis` — jobs on the `default` redis connection (DB 0); the `queue`
  service is the consumer (`ResetPasswordNotification` is the first queued job).
- `LOG_LEVEL=info`, `BCRYPT_ROUNDS=12` (explicit default).
- `MAIL_MAILER=smtp` / `MAIL_HOST=mailpit` / `MAIL_PORT=1025` /
  `MAIL_FROM_ADDRESS=no-reply@planner.local` — every mail is captured by the mailpit
  service (UI on host port 8025); nothing is ever delivered to a real inbox.

After the mariadb volume is initialized, `MARIADB_DATABASE/USER/PASSWORD` are no longer re-read —
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
- **`config/queue.php` + `ResetPasswordNotification`**: redis-backed queue (env-driven
  connection, tests force `sync`); the notification implements `ShouldQueue` so password-reset
  mail is delivered by the worker, with terminal failures recorded in `failed_jobs`.

## Environment Variables: Host → Container

| Compose value | Host `.env` key | Purpose |
|---|---|---|
| `env_file: .env` (php-fpm, queue) | (all) | full app env (`DB_*`, `REDIS_*`, `SESSION_*`, `QUEUE_*`, `APP_KEY`, `MAIL_*`, ...) |
| mariadb `MARIADB_*` | `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD:?`, `MARIADB_ROOT_PASSWORD:?` | DB/user creation (first init only) |
| redis `REDIS_PASSWORD` + `$$REDIS_PASSWORD` in command | `REDIS_PASSWORD:?` | `requirepass` |
| `REDISCLI_AUTH` | `REDIS_PASSWORD:?` | healthcheck auth |
| `${VAR:?msg}` guards | `APP_KEY`, `DB_PASSWORD`, `MARIADB_ROOT_PASSWORD`, `REDIS_PASSWORD` | required — Compose aborts if empty |

`$$` escapes a literal `$` from Compose interpolation so the container expands it at runtime
(redis command); plain `${VAR}` is interpolated at parse time (mariadb healthcheck). No
`MARIADB_PWD` anywhere (see shared-conventions).

## Decisions Log

Chronological; the durable "why" for each significant choice. Details are inline in the
file-by-file sections above; this is the one-line record.

1. **Multi-stage images, zero bind mounts** — the repo is the artifact; a fresh machine just
   builds. Rejected bind-mounting code: host paths/perms/deps would leak in, nothing to hand
   over.
2. **Build assets in BOTH images** — php-fpm renders `@vite`, nginx serves files. Rejected a
   shared-asset builder volume: ordering hacks; duplicating with identical pins is simpler.
   *(Superseded by row 30 — one build graph, each image still carries its own copy.)*
3. **Pinned base/tool versions** (`php:8.4-fpm`, `mariadb:12.3`, `redis:8.10-alpine`,
   `nginx:1.30-alpine`, `node:24-slim`, Composer phar 2.10.3 + sha256, pecl `redis-6.3.0`) —
   patch updates come free per rebuild; minor moves are deliberate acts.
4. **No custom php.ini / pool conf** — base image ships production-sane defaults incl.
   opcache (verified loaded).
5. **Redis for sessions+cache, MariaDB for data** — Redis runs without a volume (loss = forced
   re-login, acceptable).
6. **`phpredis` client** (PHP extension) — the config default.
7. **`SESSION_ENCRYPT=true`**, **`SESSION_EXPIRE_ON_CLOSE=false`** (persistent cookie; the
   skeleton's `true` made no sense for this app).
8. **`Route::block()` + Livewire update-route redeclaration** — serialize overlapping
   requests once sessions live in Redis.
9. **`trustProxies('*')`** — behind our only proxy (nginx sets `X-Forwarded-*`). Rejected a
   proxy IP list: the host's Docker bridge IP varies.
10. **Entrypoint guards `APP_KEY`, runs `migrate --seed --force` + `optimize` on every boot
    ** (migrate skipped on the queue via `SKIP_MIGRATE=1`, row 35) — see shared-conventions
    for the seeding caveat.
11. **Dedicated redis `session` connection (DB 2)** — key-space isolation from cache/default.
12. **Single named network; only nginx (80) and the Mailpit UI (8025) publish ports** —
    MariaDB/Redis unreachable by accident. Mailpit is the deliberate exception: a capture
    tool nobody can reach is useless; it has no auth, which is acceptable on the LAN and
    revisit-worthy (`MP_UI_AUTH`) if that ever changes.
13. **Named volumes `database` + `storage`**; `bootstrap/cache` deliberately NOT a volume
    (regenerated by `optimize` each boot).
14. **Log rotation 10m × 3 everywhere**, **`restart: unless-stopped` everywhere**,
    **healthcheck chain** mariadb/redis → php-fpm → nginx + `/up`.
15. **Static container names + `server_name _`** — predictable, small-scale, not swarm.
16. **Small hardening**: `server_tokens off`, security headers, dotfile-deny, `/build`
    immutable cache, `client_max_body_size` above nginx's 1 MB default.
17. **`laravel/.env.production` committed; `.env` ignored.**
18. **`unzip` in the builder** (`--prefer-dist` + PECL tarballs); `curl` deliberately not in
    the builder apt — the pinned-phar download uses the `curl` binary that ships in the php
    base image (an apt-installed curl was a leftover of the old composer curl-installer).
19. **Secrets enforced at compose level** `${VAR:?msg}` + entrypoint `APP_KEY` format guard —
    replaced old `:-root`/`:-` fallbacks that silently defaulted to weak/empty values.
20. **`start_period: 30s` on mariadb** — first boot init is slow; without it mariadb flips
    "unhealthy" during init.
21. **Production stage installs runtime libs only** (`libicu76 libonig5 libzip5`) — supersedes
    the earlier `-dev` stance: the `-dev` packages carried ~55MB of headers/static libs for
    nothing. The names are pinned to the base's Debian release (trixie today): a release move
    breaks them **loudly** at `apt-get install` (build fails, nothing reaches runtime); the fix
    is reinstalling the old names' package set in the last working image (`dpkg -l`) —
    ~3 names. The in-Dockerfile comment marks the failure site.
22. **`npm ci --no-audit --no-fund`** — reproducible from lockfiles, quiet, fast.
23. **Debian for anything glibc/PHP-bound, Alpine where it loses nothing** — `php:8.4-fpm`
    and the node stages stay Debian; `redis` and `nginx` are `-alpine` (row 34; they were
    Debian once when the project-wide no-alpine rule stood). mariadb has no Alpine variant.
    nginx:alpine ships no curl, so its stage `apk add curl` for the healthcheck.
24. **Healthchecks real, not just "alive"** — mariadb `SELECT 1` (not `mariadb-admin ping` which
    passes on failed auth), nginx `curl -fs /up`, redis `ping` via `REDISCLI_AUTH`, php-fpm
    TCP 9000; all exec-form, no shell.
25. **Layer-cached `composer install`** — only `composer.json`/`composer.lock` precede the
    install so app-code edits don't invalidate dependency layers; `package:discover` runs
    after the full source copy.
26. **Mailpit (`axllent/mailpit:v1.27`) replaces the framework `log` mailer in both stacks** —
    the app sends over SMTP to `mailpit:1025`, everything is read in the UI on 8025. No
    compose healthcheck (the image ships `/mailpit readyz`); php-fpm/queue `depends_on` it as
    `service_started` (boot ordering, still never *blocks* boot — row 38); no volume
    (disposable).
27. **Redis persistence (`--appendonly yes` + `planner_redis` volume), both stacks** —
    precondition for the redis-backed queue: jobs must survive restarts; supersedes row 5's
    no-volume stance (sessions/cache persisting too is harmless).
28. **`queue` worker service** (same `production` image as php-fpm, `queue:work --tries=3
    --sleep=1`, ordered after healthy php-fpm) — a queue with no consumer piles up silently,
    worse than sync. `entrypoint.sh` ends `exec "$@"` so the image `CMD` picks the process.
29. **Mailpit UI left unauthenticated on 8025** — deliberately skipped again in the UC-1
    step-3 security pass: temporary capture tool, not part of later production; row 12's
    `MP_UI_AUTH`/loopback revisit stands if the stack ever serves anyone beyond this machine.
30. **One Dockerfile, one build graph, one app image** — `docker/production/Dockerfile`
    (targets `production` + `nginx`) replaced the separate php-fpm/nginx Dockerfiles: nginx
    was running a second composer install + npm ci + vite build, and the queue built an
    image identical to php-fpm's. Shared stages make those runs once per clean build; nginx
    copies `public/` and the queue runs `planner-app:latest` with no build section.
    Supersedes row 2's duplication stance (each image still carries its own asset copy —
    the copies just come from one build now).
31. **Prod drops `gd` + `bcmath`** — same zero-call-site evidence as dev (repo-wide grep,
    composer.lock only `suggest`, no hard requires; prod's `--no-dev` dependency set is a
    subset of dev's). Removes the gd compile + its png/jpeg/freetype `-dev` libs.
32. **Production stage swaps `-dev` for runtime lib names** — row 21's stance reversed with
    measurements: `-dev` overhead was ~55MB installed (libicu-dev alone 50MB); `ldd` on the
    compiled `.so`s gives the exact link set (intl→libicu, mbstring→libonig, zip→libzip;
    redis/pdo_mysql link nothing apt-provided).
33. **Composer = pinned phar in prod too** — same 2.10.3 + sha256 block as
    `docker/development/php-cli/Dockerfile`; drops the `composer:2.10` donor image (~180MB
    pull on a fresh machine) and makes both stacks bump the composer version in one edit
    (two places, same values).
34. **Redis and nginx go `-alpine` in prod** — `redis:8.10-alpine` (byte-identical service
    definition to dev's, which was already alpine; AOF volume format is cross-compatible) and
    `nginx:1.30-alpine` (~190MB smaller; `apk add curl` keeps the exact `curl -fs /up`
    healthcheck). Supersedes row 23's all-Debian stance.
35. **Queue gets `SKIP_MIGRATE=1`** — php-fpm remains the boot-time migrator/seeder (queue
    starts only after its healthcheck), so the worker skips the redundant migrate run and
    goes straight to `optimize` + `queue:work`. Behavior change from "both containers migrate
    on boot" — approved in the step-2/3 batch.
36. **`pcntl` joins the prod ext set** — `queue:work` installs its SIGTERM handlers only when
    pcntl is loaded; without it `docker stop` kills the worker mid-job (the job retries via
    `--tries`, but nothing ever drains cleanly). Dev's image already had pcntl (the pest
    browser server needs it); prod was the gap. Gate: `php -m` shows `pcntl` in
    `planner-app:latest`, proven live — stopping the queue container exits **0** with a
    `Worker STOPPED Interrupted` log line (without pcntl the worker dies on SIGTERM with
    exit 143 and no clean drain).
37. **`COPY --chown=www-data` replaces `RUN chown -R /var/www`** — a `chown -R` layer repacks
    every file's content (vendor included) into its own layer; COPY sets the ownership in
    passing at zero cost. `public/build` deliberately stays root-owned (the app only reads
    the manifest); everything writable — `storage/`, `bootstrap/cache/` — comes from the
    chowned copy. **Caveat found at the gate:** COPY owns the *contents*, not the
    destination directory itself — `/var/www` stayed root-owned, so tinker died with
    psysh's "Writing to directory .config/psysh is not allowed" (psysh creates dot-dirs in
    the project root). Fix: one non-recursive `RUN chown www-data:www-data /var/www`
    after the COPYs — a single metadata-only entry, no file repack. Gate: `stat` shows
    `/var/www` as www-data while `public/build` remains root.
38. **php-fpm and queue `depends_on` mailpit (`service_started`)** — amends row 26: any mail
    sent right after boot reaches a listening SMTP socket instead of racing it. `service_started`
    (not `healthy`) keeps boot instant — mailpit is up in milliseconds and its capture is
    disposable, so nothing waits on readiness.

## Deployment Runbook (fresh machine, golden path)

Prerequisites: Docker Engine with Compose v2 (Docker ≥ 24) and git.

```bash
git clone <repo-url> planner && cd planner
cp laravel/.env.production laravel/.env
```

Fill the secrets in `laravel/.env`:

```bash
APP_KEY=$(printf 'base64:%s' "$(openssl rand -base64 32)")   # never reuse from elsewhere
# also set: DB_PASSWORD, MARIADB_ROOT_PASSWORD, REDIS_PASSWORD
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
| Fresh APP_KEY | on your machine: `printf 'base64:%s\n' "$(openssl rand -base64 32)"`, then `.env`, recreate php-fpm + queue |
| Shell into a service | `exec php-fpm sh` (runs as `www-data`) |
| Manual DB backup (no automation yet) | `exec mariadb sh -c 'mariadb-dump -u planner -p"$MARIADB_PASSWORD" planner' > backup.sql` |

APP_KEY rotation (rare; forces re-login): generate, edit `.env` → `up -d --force-recreate
php-fpm queue nginx`, verify `/up` and a fresh login.

### Env-change workflow (why it works)

Compose re-reads `.env` on every command, but a running container freezes its env at start.
So: change `.env` → recreate the container that needs it. php-fpm's entrypoint runs
`optimize` on boot, so cached config is rebuilt automatically (no manual `config:clear`).

## Verification & Troubleshooting

| Symptom | Check | Fix |
|---|---|---|
| `up` aborts "required variable … missing a value" | `.env` has a blank secret | fill the named var |
| "Unsupported cipher or incorrect key length" | `APP_KEY` missing the `base64:` prefix | regenerate + `up -d --force-recreate php-fpm queue` |
| `502 Bad Gateway` | is php-fpm healthy? | `up -d php-fpm`; check `logs php-fpm` |
| `/up` 500 / "whoops" | php-fpm logs | app exception; check memory, APP_KEY validity |
| Container restart-loop | `logs` shows migration error | mariadb not ready (wait; `start_period` covers first boot) or DB creds wrong |
| MariaDB won't accept new creds | volume already initialized | fix the DB user instead (or recreate volume deliberately) |
| Redis NOAUTH errors | `.env` `REDIS_PASSWORD` vs container env | edit `.env`, recreate redis + php-fpm |
| Everything healthy but login fails | session store | `exec redis redis-cli -a "$REDIS_PASSWORD" FLUSHALL` (nukes sessions+cache+jobs — queued mail is lost, users just re-request; row data intact) |
| Queued mail never arrives | `./planner logs queue` + `failed_jobs` table | worker down → `up -d queue`; job exhausted retries → inspect the `failed_jobs` row |
| `inspect … .Image` ≠ `images -q` after a rebuild (looks stale) | sha256 of a real file in the running container vs `docker run --rm --entrypoint sha256sum <image> <file>` | usually benign: with the containerd image store both values are *attested index* digests, which change every build (provenance metadata) even when content is identical — compose compares content manifests, so it skips recreation correctly; only chase it if the content hashes differ |
| Port 80 busy on host | `ss -ltnp` | stop the other process, or map another port in compose |

Healthcheck definitions (for diagnosis):

| Service | Test | interval / timeout / retries / start |
|---|---|---|
| php-fpm | `php -r` fsockopen TCP 9000 | 10s / 3s / 5 / 30s |
| nginx | `curl -fs http://127.0.0.1/up` (full stack) | 30s / 3s / 3 / 5s |
| mariadb | `mariadb -uroot -p"${MARIADB_ROOT_PASSWORD}" -e SELECT 1` (exec CMD, real auth) | 10s / 5s / 5 / 30s |
| redis | `redis-cli ping` (auth via `REDISCLI_AUTH`) | 10s / 5s / 5 |
| mailpit | image built-in `["/mailpit", "readyz"]` (no compose stanza) | image default 15s / — / 3, start-period 10s |

`depends_on: condition: service_healthy` orders them: mariadb+redis → php-fpm → nginx + queue
(queue: no port → no healthcheck); php-fpm and queue additionally wait for mailpit
`service_started` (SMTP reachable before any send).

## Docker Hygiene on This Host

Expected state: project images plus build bases pulled on demand, the `database`,
`storage`, and `redis` volumes, no dangling images. Rules forever:

- **Never** `docker system prune -a --volumes`, `docker volume prune`, or `down -v` on a live
  install — those delete `planner_database` / `planner_storage` / `planner_redis`.
- Safe weekly-ish hygiene: `docker system prune -f` (dangling images) and `docker builder
  prune -f`.
- Build bases are ephemeral — deleting them is fine; the next `up --build` re-pulls.

## Keeping This Setup Updated

Structural truth: **the images are immutable**; the repo defines the deployment. Updates are
"change pins/files, rebuild, verify."

- App/config changes → `git pull && up -d --build && curl -s http://localhost/up` + smoke
  login. Both final images come from one Dockerfile/context (no version skew).
- Dependency bumps (`composer.lock` / `package-lock.json`): bump in dev, commit
  both lockfiles, rebuild. Never hand-edit locks in prod; never `up --build` dirty.
- Framework upgrade: treat like a dependency bump but re-check `bootstrap/app.php` /
  `config/database.php` against the new skeleton and re-run the config-defaults audit.
- Base-image patch updates: free via rebuild (all tags are MAJOR.MINOR). Minor updates:
  deliberate — bump in the Dockerfile / compose, rebuild, full verification, update the
  version table and decisions log.
- Env surface changes: add to `laravel/.env.production` + a new decisions-log row; live
  installs need the same manual edit + recreate flow.
- Adding infrastructure (scheduler, scale-out): copy the queue-worker pattern (same app
  image + `command` override, ordered after healthy php-fpm, shared logging/restart model).
  Redis already has the persistence a Redis-backed queue requires — keep it. Out of current
  scope unless a feature requires it.
- Editing `docker/`: keep `.dockerignore`, `.env.*` templates, the Dockerfile and
  `default.conf` coherent; rebuild + health-verify; update this doc.
  The comment-free style in the images is intentional — the "why" lives here.
- Composer pin guard: the phar 2.10.3 + sha256 block exists in **both** Dockerfiles (dev
  builder and prod builder) — bump version and checksum together in both. The prod build
  verifies the checksum, so a corrupted download fails the build instead of shipping.

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
- **CDN / compression pre-bake**: unnecessary or contrary to size.

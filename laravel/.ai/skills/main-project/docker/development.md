# Docker Development Setup

> Reference documentation. Read this before touching anything under `docker/`,
> `laravel/compose.development.yml`, or the `.env.*` templates. Shared conventions
> (secrets, healthchecks, wrappers) live in `docker/shared-conventions.md`.

## Purpose and Audience

Describes the local development stack: one workspace container with the full PHP + Composer
+ Node toolchain, MariaDB and Redis for prod parity. It is not user documentation.

## Current Status (snapshot)

| Item | Value |
|---|---|
| Orchestration | Docker Compose v2 (`laravel/compose.development.yml`), project `planner-development` |
| Workspace image | `php:8.4-cli` (Debian/glibc) built from `docker/development/php-cli/Dockerfile` |
| Build stages | `composer:2.10`, `node:24` (toolchain; never installed in-image) |
| Services | `planner-development-workspace` + `-mariadb` + `-redis` + `-mailpit` |
| Volumes | `database` + `redis` (appendonly) + `playwright` (Chromium; workspace is stateless) |
| Host ports | `8000` (artisan serve), `5173` (Vite HMR), `8025` (Mailpit UI) — MariaDB/Redis/SMTP expose none |
| PHP extensions | `pdo_mysql, pdo_sqlite, intl, mbstring, zip, bcmath, gd, sockets, pcntl, redis` |
| Tests | Pest on SQLite `:memory:` — `pdo_sqlite` is required |
| Restart / logging | `unless-stopped`; `json-file` capped 10m × 3 |

## Target Dev Model

- One terminal with everything: `artisan serve`, Vite, migrations, tests, Tinker.
- No nginx/php-fpm in dev; Redis *is* present for session/cache/queue prod parity.
- Repo bind-mounts `./` → `/var/www`; app-code changes never need a rebuild.
- Container runs as host UID/GID (`user: ${UID:-1000}:${GID:-1000}`), `HOME=/home/workspace`
  — caches writable, no permission fights, files owned by the host user.
- **Nothing runs on boot.** No entrypoint; image ends at `CMD ["sleep", "infinity"]`.
  Deps install once, manually, on the bind mount (`composer install` / `npm install`) and
  persist there. MariaDB data survives restarts in its own volume.

## Architecture

```
        Host
  ┌──────────────┬────────────────┐
  │  :8000 ◄─────┘   :5173        │
  ▼                              │
 planner-development-workspace ◄─ bind mount ./:/var/www
  artisan serve 0.0.0.0:8000      │
  Vite dev    0.0.0.0:5173        │
 ──► mariadb (DNS, :3306)        │
 ──► redis (DNS, :6379)          │
 ──► mailpit (DNS, :1025)        │
 └──────────┬───────────────────┘
planner-development-mariadb (mariadb:12.3, data in database volume)
planner-development-redis (redis:8.10)
planner-development-mailpit (axllent/mailpit:v1.27, UI published on host :8025)
```

Service names are DNS: `DB_HOST=mariadb`, `REDIS_HOST=redis`, `MAIL_HOST=mailpit`. Only the
workspace and the Mailpit UI publish
host ports. Secrets arrive by compose interpolation, not `MARIADB_PWD` (see shared-conventions).

## Repository Layout (Docker Parts)

```
/
├── planner / planner-dev           command wrappers (see shared-conventions)
├── .dockerignore                   build-context filter (with comments)
├── docker/development/php-cli/     Dockerfile (dev toolchain image)
├── docker/production/              prod images (see production.md)
└── laravel/
    ├── compose.development.yml     dev stack definition
    ├── compose.production.yml      prod stack definition
    ├── .env.development            committed dev template (source of truth)
    ├── .env                        real values, git-ignored
    ├── vite.config.js              container-specific Vite settings
    └── .ai/skills/main-project/docker/  development.md + production.md + shared-conventions.md
```

## File-by-File

### `laravel/compose.development.yml`

Single-file Compose v2 spec. Facts:

- **workspace**: no `env_file` — Laravel reads the bind-mounted `laravel/.env` directly.
  Only `APP_KEY` is enforced via compose `${APP_KEY:?Set APP_KEY in .env}` (must already be
  in `.env` before the first `up`). `tty` + `stdin_open` keep it interactive; `depends_on`
  waits for both mariadb and redis health.
- **mariadb**: `mariadb:12.3`, `MARIADB_ROOT_PASSWORD`/`DB_PASSWORD` required via `${VAR:?}`, data in
  the `database` volume, exec-form healthcheck `mariadb -uroot -p"${MARIADB_ROOT_PASSWORD}" -e
  'SELECT 1'` with `start_period: 30s`. **Never set `MARIADB_PWD`** (breaks first-boot init).
- **redis**: `redis:8.10`, `requirepass` via `REDIS_PASSWORD`, `--appendonly yes` with a
  `redis:/data` volume (jobs in flight survive `down`/`up`), healthcheck `redis-cli ping`
  authed through `REDISCLI_AUTH`.
- **mailpit**: `axllent/mailpit:v1.27`, publishes only the web UI (`8025:8025`); SMTP 1025
  stays internal (other containers reach `mailpit:1025`). No compose healthcheck — the
  image ships a built-in exec-form `HEALTHCHECK ["/mailpit", "readyz"]`. Nothing
  `depends_on` it; a stopped Mailpit must never block the app.
- **`playwright` named volume** on `/home/workspace/.cache/ms-playwright` — keeps the
  downloaded Chromium across `down`/`up` (only the system libraries are baked into the
  image). Created root-owned on first use; the image's startup `chown` normalizes it.
- Credentials are baked when the mariadb volume is first created; changing `MARIADB_*` later
  needs the volume reset (see Troubleshooting).

### `docker/development/php-cli/Dockerfile`

Multi-stage toolchain image. Structure:

- `FROM node:24 AS node`, `FROM composer:2.10 AS composer`, `FROM php:8.4-cli`.
- apt installs only runtime deps: `libonig-dev libicu-dev libzip-dev libpng-dev libjpeg-dev
  libfreetype6-dev libsqlite3-dev` + `curl unzip git ca-certificates`; then
  `docker-php-ext-configure gd --with-freetype --with-jpeg` and installs
  `pdo_mysql pdo_sqlite intl mbstring zip bcmath gd sockets pcntl`; `pecl install redis`
  + enable. `sockets`/`pcntl` serve pest-plugin-browser's in-process Amp server — without
  `pcntl`, every run ends in a SIGTERM backtrace.
- **`npx -y playwright@1.64.0 install-deps chromium`** bakes the browser's system
  libraries; the version must match `laravel/package.json`'s exact pin or deps and browser
  drift apart. The Chromium download itself goes to the `playwright` volume (one-time
  `npx playwright install chromium` — README step 8).
- **Debian, not Alpine** — Node's alpine builds are musl and cannot run in a glibc PHP
  image; `node:24` (no suffix) is glibc, the exact tag prod uses.
- composer phar copied from the composer stage; node + npm/npx from the node stage (npm/npx
  symlinked into `/usr/local/bin`). Node source install rejected (Python/pip cruft).
- `HOME=/home/workspace` (created, owned 1000:1000) so `~/.npm`, `~/.composer`, `~/.git`
  are writable — the earlier `EACCES /.npm` failure.
- Build-time version guards (`node --version`, `npm --version`, `composer --version`,
  `php -m | grep redis`) fail the build loudly instead of surprising at runtime.
- `CMD ["sleep", "infinity"]`, no `ENTRYPOINT` — nothing runs automatically.

### `laravel/.env.development`

Committed template. Real values live only in the git-ignored `.env`. Notable
defaults:

- `APP_ENV=local`, `APP_DEBUG=true`, `LOG_LEVEL=debug`.
- `DB_HOST=mariadb`, `REDIS_HOST=redis` (service names as hostnames).
- `SESSION_DRIVER=redis` / `CACHE_STORE=redis` / `QUEUE_CONNECTION=redis` — prod parity through the real Redis stack.
- `SESSION_ENCRYPT=false` — the one deliberate divergence from prod, so dev tools can read
  sessions while debugging.
- `MAIL_MAILER=smtp` / `MAIL_HOST=mailpit` / `MAIL_PORT=1025` /
  `MAIL_FROM_ADDRESS=no-reply@planner.local` — every mail is captured by the mailpit
  service and read in its UI at `http://localhost:8025`; nothing ever leaves the host.
- `APP_URL=http://localhost:8000` — reset-email links root here (never the request host),
  so they stay clickable from the Mailpit UI on this machine even when the app is browsed
  through another hostname.
- Removed from stock Laravel: BROADCAST/FILESYSTEM/MEMCACHED/AWS/VITE (each has a
  sane default or is unused).
- Only these blanks to fill: `APP_KEY`, `DB_PASSWORD`, `MARIADB_ROOT_PASSWORD`, `REDIS_PASSWORD`.

### `vite.config.js`

Container-specific settings: `server.host: '0.0.0.0'` (reachable via the host port
—— Docker forwards to eth0, not loopback), `hmr.host: 'localhost'`, and
`watch.usePolling: true` (reliable file-watching on the bind mount). Nothing else changes.

### App-side wiring (read-only)

- `composer.json`: `composer run dev` (concurrent `artisan serve` + `queue:listen` — the
  dev consumer for the redis queue — + `npm run dev`), `composer test`.
- `phpunit.xml`: `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` and `QUEUE_CONNECTION=sync`
  + `MAIL_MAILER=array` — tests run with no DB container and execute jobs/notifications
  inline, anywhere with `pdo_sqlite`.

## Environment Variables: Host → Container

| Compose/deploy value | Source in `.env` | Notes |
|---|---|---|
| `workspace` env | mounted `laravel/.env` | Laravel reads it from the bind mount |
| `workspace.environment.APP_KEY` | `${APP_KEY:?}` | compose-level guard |
| `MARIADB_*` | `${MARIADB_ROOT_PASSWORD:?}`, `${DB_DATABASE}`, `${DB_USERNAME}`, `${DB_PASSWORD:?}` | requirements + DB name/user; baked at first volume init |
| `REDIS_PASSWORD` / `REDISCLI_AUTH` | `${REDIS_PASSWORD:?}` | `requirepass` + healthcheck auth |
| `workspace` mail config | `MAIL_*` (mounted `laravel/.env`) | SMTP to `mailpit:1025`; UI published on host :8025 |

Keep the credential values in sync between compose interpolation and the workspace's `.env`
— both derive from the same file.

## Runbook (Fresh Machine)

1. Prerequisites: Docker with Compose v2, Git. 
2. `git clone <repo-url> planner && cd planner`.
3. Copy the template: `cp laravel/.env.development laravel/.env` (macOS/Linux) or
   `Copy-Item laravel/.env.development laravel/.env` (PowerShell).
4. Fill the blanks in `laravel/.env`. `APP_KEY` **must exist before the first `up`**
   (compose enforces it). Generate on the host:
   `printf 'base64:%s\n' "$(openssl rand -base64 32)"` (Windows: build the same from
   `[System.Security.Cryptography.RandomNumberGenerator]::GetBytes(32)`).
   Set `DB_PASSWORD`, `MARIADB_ROOT_PASSWORD`, `REDIS_PASSWORD` before the first `up` too —
   MariaDB bakes them when the volume is created.
5. Build + start: `./planner-dev up` (or the raw compose form). First run is slow (pulls all
   base images, compiles extensions).
6. Shell in: `./planner-dev shell`, then once: `composer install` and `npm install` (they
   persist on the bind mount). For the browser test tier also run once:
   `npx playwright install chromium` (README step 8).
7. Start servers: `php artisan serve --host=0.0.0.0 --port=8000` (the `0.0.0.0` is required),
   `npm run dev`, optional `php artisan queue:listen --tries=1`. Or `composer run dev` for all
   three.
8. First DB use: `php artisan migrate`, `php artisan db:seed` (optional dev account).
9. Check: browser → `http://localhost:8000`; `http://localhost:8000/up` returns 200.

## Day-to-Day Operations

All from the project root. `down` never deletes data.

| Task | Command |
|---|---|
| Start / stop | `./planner-dev start` / `stop` |
| Plain up | `./planner-dev up` |
| Rebuild toolchain only | `docker compose -f laravel/compose.development.yml up -d --build` (only when the Dockerfile changes — reload app code for free via bind mount) |
| Shell | `./planner-dev shell` |
| Logs | `./planner-dev logs [service]` |
| Browser tests (opt-in tier) | `composer test:browser` (inside the shell; not included in plain `composer test`) |

**Never** `down -v` — it wipes the MariaDB dev volume.

Inside the workspace daily loop: `artisan serve --host=0.0.0.0 --port=8000`, `npm run dev`,
`composer test`, `php artisan migrate`, `php artisan tinker`.

## Verification & Troubleshooting

### Checking the stack

```bash
docker compose -f laravel/compose.development.yml ps   # workspace + mariadb + redis + mailpit healthy
# inside: node --version && npm --version && composer --version && php -v && php -m | grep redis
# host:   curl -s http://localhost:8000/up             # expect 200
```

### Troubleshooting table

| Symptom | Cause / fix |
|---|---|
| `npm: command not found` | image predates the node stage — `up -d --build` |
| npm `EACCES` on `/.npm` | `HOME` must be `/home/workspace` (already in image); verify `echo $HOME` |
| `up` aborts `Set APP_KEY in .env` | fill a valid `base64:` key then `up` again |
| deps "no such file" on fresh clone | nothing auto-installs — run `composer install` + `npm install` once |
| Composer platform issues on install | toolchain changed — rebuild via `up -d --build` |
| port 8000/5173 already in use | free the port or edit `ports:` in the compose file |
| mariadb unhealthy / `Access denied for user 'planner'` | baked creds differ from current `.env` — dev data is disposable: `down`, `docker volume rm planner-development_database`, `up -d --build`, re-`migrate` |
| redis `NOAUTH` / `WRONGPASS` | `.env` `REDIS_PASSWORD` mismatch — make it match and `up -d --force-recreate redis` |
| password-reset mail never appears | queue worker not running (`composer run dev` includes `queue:listen`) or mailpit down / `MAIL_HOST` wrong — start the dev script, `./planner-dev up`, `logs mailpit`, UI at `http://localhost:8025` |
| `artisan serve` runs but browser times out | forgot `--host=0.0.0.0` |
| Vite not hot-reloading | `usePolling` is on; ensure 5173 is free and reach via `localhost:5173` |
| tinker `app@... does not exist` | run `composer install`, then `php artisan package:discover`, restart tinker |
| `composer test:browser` → "Executable doesn't exist ... chrome-linux" | no browser in the `playwright` volume yet — `npx playwright install chromium` once in the shell |
| browser test run ends in a `SIGTERM`/`pcntl` backtrace | image predates the `pcntl` extension — `./planner-dev build && ./planner-dev up` |
| browser test fails with `EACCES` under `~/.cache/ms-playwright` | volume root-owned on an old image — rebuild, or `docker exec -u root <workspace> chown -R 1000:1000 /home/workspace/.cache/ms-playwright` |
| Playwright/Chromium mismatch after a toolchain bump | `package.json`'s pin and the Dockerfile's `playwright@X` must move together — bump both, rebuild, re-run the Chromium install |

## Future Notes, Desires, and Not-Done

- `php artisan serve` without flags still binds `127.0.0.1`; the `dev` script and runbook pass
  `--host=0.0.0.0` explicitly. Auto-binding would mean overriding Laravel's built-in command —
  deferred.
- `SESSION_ENCRYPT=false` is prod-only; flip it in the dev template to match prod exactly if
  it ever matters.
- MariaDB/Redis on a host port for a GUI client is possible but deliberately not done — no host
  exposure, matching prod.
- prod ↔ dev on one host: both read the same `laravel/.env`; only one runs at a time
  (re-copy template + re-fill secrets to switch). Volumes are separate per stack.

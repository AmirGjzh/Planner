# Docker Production Setup

> Snapshot documentation. Read this before touching anything under `docker/`,
> `laravel/compose.production.yml`, `.dockerignore`, or the `.env.*` templates.

## Purpose and Audience

This document is the single source of truth for the Docker production deployment of the
Planner application. It exists (1) so a future "me" — potentially two years from now — can
recall every decision, file, and workflow without re-deriving anything, and (2) so agents /
LLMs can work on this setup without needing to re-discover the architecture.

It is **not** user documentation. The end user never sees this file.

---

## Current Status (snapshot)

| Item | Value |
|---|---|
| Deployment model | Single host, LAN, plain HTTP, no domain, personal/small usage |
| Orchestration | Docker Compose v2 (`laravel/compose.production.yml`) |
| App | Laravel 13.x + Livewire 4.x (see `composer.json`) |
| PHP-FPM image | `php:8.4-fpm`, multi-stage, runs as `www-data` |
| Nginx image | `nginx:1.30` (running 1.30.4) |
| MySQL image | `mysql:8.4` (running 8.4.11) |
| Redis image | `redis:8.10` (running 8.10.1) |
| Composer image (build-only) | `composer:2.10` (2.10.3) |
| Node image (build-only) | `node:24` |
| Containers | `planner-php-fpm`, `planner-nginx`, `planner-mysql`, `planner-redis` |
| Named volumes | `planner_database`, `planner_storage` |
| Network | single user network `network` → prefixed `planner_network` |
| Restart policy | `unless-stopped` on all services |
| Logging | `json-file`, max-size 10m, max-file 3, on all services |

Runtime state at the time of writing: all 4 containers healthy, `/up` returns 200,
`laravel/.env.production` values match the app's config defaults (verified in the audit).

---

## Target Deployment Model

- One Linux host (this machine), inside the private LAN, accessed as `http://localhost`
  or `http://<host-ip>`.
- All four services run on one Docker host. Nothing is exposed to the public internet.
- No HTTPS, no reverse-proxying CDN, no domain name. TLS was deliberately deferred
  (see [Explicitly Deferred](#explicitly-deferred-and-not-done)).
- The repo is the deliverable: a fresh machine needs a clone, the filled-in `.env`, and
  `docker compose up --build`, then runs forever.

---

## Architecture

```
        Browser (LAN)
             │  http://<host>:80
             ▼
   ┌───────────────────┐
   │  planner-nginx    │  serves /var/www/public (built assets) + routes PHP to fpm
   │  nginx:1.30       │  /up healthcheck; publishes port 80 only
   └─────────┬─────────┘
             │  fastcgi → php-fpm:9000
             ▼
   ┌───────────────────┐   depends_on (healthy)      ┌───────────────────┐
   │  planner-php-fpm  │ ──────────────────────────► │  planner-mysql    │
   │  php:8.4-fpm      │   runs migrations+optimize  │  mysql:8.4        │
   │  runs the app     │   each boot, then php-fpm   │  data in volume   │
   └─────────┬─────────┘                             │  database         │
             │  redis (sessions/cache/locks)         └───────────────────┘
             ▼
   ┌───────────────────┐
   │  planner-redis    │
   │  redis:8.10       │  requirepass, no volume (ephemeral)
   └───────────────────┘
```

Key facts:

- Containers reach each other by **container name as DNS** inside `planner_network`:
  `mysql`, `redis`, `php-fpm`, `nginx` (upstream `fastcgi_pass php-fpm:9000`).
- **Only nginx** publishes a host port (`80:80`). MySQL/Redis are not exposed to the host;
  the database is reachable only from inside the network.
- nginx cannot run PHP; it proxies to php-fpm. php-fpm cannot serve files to the browser
  directly for page assets; nginx serves them from the shared image content.
- php-fpm needs `public/build/manifest.json` to render `@vite` tags; nginx needs the same
  files to serve them. Therefore **assets are built twice**, once in each image's Dockerfile,
  with the same pins and lockfiles so the output is identical.
- The app container is the only one that runs Laravel code; entrypoint runs setup on every
  boot (see [entrypoint.sh](#dockerproductionphp-fmpentrypointsh)).

---

## Repository Layout (Docker Parts)

```
/                                repo root (docker build context)
├── planner                              commands: ./planner up, ps, build, ...
├── planner-dev                          commands: ./planner-dev shell, up, build, ...
├── .dockerignore                        build-context-effective ignore
├── docker/
│   ├── development/
│   │   └── php-cli/Dockerfile           dev image (see development.md)
│   └── production/
│       ├── php-fpm/
│       │   ├── Dockerfile               php-fpm image
│       │   └── entrypoint.sh            container entrypoint (guards + setup + exec)
│       └── nginx/
│           ├── Dockerfile               nginx image
│           └── conf.d/default.conf      vhost
├── laravel/                             app code + stack definitions
│   ├── compose.production.yml           prod stack definition
│   ├── .env.production                  committed prod template (source of truth)
│   ├── .env.development                 committed dev template (see development.md)
│   ├── .env                             real values, git-ignored, copied from a template
│   ├── bootstrap/app.php                app-level HTTP config (trusted proxies, redirects)
│   ├── config/database.php              redis connections (default/cache/session)
│   ├── routes/web.php                   all routes + block(10,10)
│   └── app/Providers/AppServiceProvider.php
│                                        Livewire update route, block(10,10)
```

Files marked APP-SIDE below are application changes made specifically for this
deployment model — they ship with the app, not the images.

---

## File-by-File

### `laravel/compose.production.yml`

Single-file Compose v2 spec. The full file:

```yaml
name: planner

services:
  php-fpm:
    container_name: planner-php-fpm
    build:
      context: ..
      dockerfile: ./docker/production/php-fpm/Dockerfile
      target: production
    restart: unless-stopped
    env_file:
      - .env
    environment:
      APP_KEY: ${APP_KEY:?Set APP_KEY in .env}
    volumes:
      - storage:/var/www/storage
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
    networks:
      - network
    depends_on:
      mysql:
        condition: service_healthy
      redis:
        condition: service_healthy
    healthcheck:
      test: ["CMD", "php", "-r", "exit(@fsockopen('127.0.0.1', 9000) ? 0 : 1);"]
      interval: 10s
      timeout: 3s
      retries: 5
      start_period: 30s

  nginx:
    container_name: planner-nginx
    build:
      context: ..
      dockerfile: ./docker/production/nginx/Dockerfile
    restart: unless-stopped
    ports:
      - "80:80"
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
    networks:
      - network
    depends_on:
      php-fpm:
        condition: service_healthy
    healthcheck:
      test: ["CMD", "curl", "-fs", "http://127.0.0.1/up"]
      interval: 30s
      timeout: 3s
      retries: 3
      start_period: 5s

  mysql:
    container_name: planner-mysql
    image: mysql:8.4
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:?Set MYSQL_ROOT_PASSWORD in .env}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD:?Set DB_PASSWORD in .env}
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
    volumes:
      - database:/var/lib/mysql
    networks:
      - network
    healthcheck:
      test: ["CMD", "mysql", "-uroot", "-p${MYSQL_ROOT_PASSWORD}", "-e", "SELECT 1"]
      interval: 10s
      timeout: 5s
      retries: 5
      start_period: 30s

  redis:
    container_name: planner-redis
    image: redis:8.10
    restart: unless-stopped
    environment:
      REDIS_PASSWORD: ${REDIS_PASSWORD:?Set REDIS_PASSWORD in .env}
      REDISCLI_AUTH: ${REDIS_PASSWORD:?Set REDIS_PASSWORD in .env}
    command: ["sh", "-c", "exec redis-server --requirepass \"$$REDIS_PASSWORD\""]
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
    networks:
      - network
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5

networks:
  network:

volumes:
  database:
  storage:
```

Rationale per decision (details in [Decisions Log](#decisions-log)):

- **`container_name`** fixed so DNS/names are predictable (`planner-php-fpm`, `planner-nginx`,
  `planner-mysql`, `planner-redis`). Names must be unique on the host.
- **`env_file: .env`** gives php-fpm the full environment; MySQL/Redis get only the values
  they need via `environment:` interpolation. Compose automatically reads the top-level
  `.env` for `${VAR}` interpolation. All four secrets (`APP_KEY`, `DB_PASSWORD`,
  `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD`) are enforced with `${VAR:?msg}` — Compose
  aborts immediately with a clear error if any is missing, so a fresh `.env` can never boot
  insecurely.
- **Volumes**: `database:/var/lib/mysql` is the real data. `storage:/var/www/storage` keeps
  runtime app storage (logs). Both are named volumes; in this project the volumes get the
  `planner_` prefix (`planner_database`, `planner_storage`) — the project name is declared
  explicitly with `name: planner` at the top of `laravel/compose.production.yml`, so the prefix no
  longer depends on the checkout directory being named `planner`.
- **No bind mounts**: the code lives inside the images (immutable artifact). Updating the
  app always means rebuilding. This is intentional — a fresh machine just builds from git.
- **Healthchecks** form the boot chain: `mysql` and `redis` must be healthy before
  php-fpm starts (so the entrypoint's `migrate` works), php-fpm must be healthy before
  nginx starts. nginx health is what an external check would use (`/up`). Each service
  tests itself — see the healthcheck table in [Verification & Troubleshooting](#verification--troubleshooting).
- **Logging** is capped (10 MB × 3 files per container) — this app is chatty by design
  (`LOG_LEVEL=info`, access logs), so unbounded Docker logs would fill the disk.
- **`restart: unless-stopped`** — survives host reboots; someone can `docker stop` a
  service and Compose won't force it back until an explicit `start`.

### `docker/production/php-fpm/Dockerfile`

The php-fpm image. Multi-stage; four stages, each a disposable workshop:

```dockerfile
FROM composer:2.10 AS composer          # (1) pinned Composer binary
FROM php:8.4-fpm AS builder            # (2) compiles extensions + vendor
FROM node:24 AS frontend        # (3) builds JS/CSS with Vite
FROM php:8.4-fpm AS production         # (4) final slim runtime
```

Build order and reasoning:

1. **composer stage**: `composer:2.10` is the official image. Both this Dockerfile and the
   nginx one copy `/usr/bin/composer` from it, so every build installs PHP dependencies with
   the identical Composer version. (Previously Composer was installed via the curl
   installer; the `composer` image is now the single source. The leftover `curl` package
   was removed from the builder's `apt-get`; `unzip` stays because `--prefer-dist` needs
   it to unpack dependency archives.)
2. **builder stage** (`php:8.4-fpm`):
   - Installs the `-dev` system libraries needed to *compile* extensions: `libonig-dev`
     (mbstring), `libicu-dev` (intl), `libzip-dev` (zip), `libpng-dev` `libjpeg-dev`
     `libfreetype6-dev` (gd with jpeg/freetype support).
   - Compiles + enables extensions: `pdo_mysql intl mbstring zip bcmath gd`.
   - Installs the Redis client via PECL and enables it (`phpredis` is the `REDIS_CLIENT`)
     — why `phpredis` over the pure-PHP `predis`/`relay`: it is the PHP-extension client
     that Laravel's config assumes by default and is inert (no worker processes) here.
   - `COPY . /var/www` (the whole build context minus `.dockerignore`), copies the Composer
     binary in, then `composer install --no-dev --no-scripts --optimize-autoloader
     --no-interaction --no-progress --prefer-dist` and `php artisan package:discover`.
     `--no-scripts` is safe because the `.env`-dependent post-install scripts are not
     present (no `.env` in the image) and the autoloader only needs the files.
   - The stage discards everything except its compiled artifacts.
3. **frontend stage** (`node:24`): `npm ci --no-audit --no-fund` from lockfile, copies
   `vendor/` from the builder stage so Vite can resolve the Livewire ESM import in
   `resources/js/app.js`, then `npm run build`. `npm ci` is used (not `npm install`) so
   builds are reproducible from `package-lock.json`; `--no-audit --no-fund` keeps the log
   quiet and fast (harmless — the lockfile is the source of truth, and Docker reads updates
   anyway).
4. **production stage** (`php:8.4-fpm`):
   - Installs the same libraries using the `-dev` Debian packages (on this Debian release
     the *versioned* runtime names differ, so the -dev packages double as the runtime
     names — hence re-installing them here rather than copying libs blindly).
   - Copies the compiled extension `.so` files, their `conf.d/*.ini`, and the
     `docker-php-ext-*` helper scripts from the builder stage.
   - `mv php.ini-production php.ini` — PHP's "production" ini (display_errors off,
     error_reporting E_ALL, log_errors on). No custom ini is added: the base image already
     loads opcache (`conf.d/docker-php-ext-opcache.ini`) with sane defaults (memory 128M,
     10000 accelerated files, validate_timestamps on in dev-off; production defaults) as
     verified in the audit — a custom ini would add no value. `expose_php=On` is left as-is;
     nginx strips `X-Powered-By` at the edge.
   - Copies the app (`/var/www` incl. `vendor/` from builder) and `public/build` from the
     frontend stage; final COPY order: builder app, then frontend assets (frontend wins).
   - `chown -R www-data:www-data /var/www` then `USER www-data` — php-fpm runs unprivileged
     and Laravel needs to create storage/bootstrap-cache dirs at runtime (the volume is
     initialized from the image content, so ownership is preserved).
   - `ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]`, `CMD ["php-fpm"]` listening on
     TCP 9000.

There is **no `composer` binary in the runtime image** — Composer is build-time only.
Verified intentionally: `exec php-fpm which composer` fails, which is expected.

### `docker/production/nginx/Dockerfile`

```dockerfile
FROM composer:2.10 AS composer          # vendor/ for Vite's Livewire import
FROM node:24 AS builder                 # build assets
FROM nginx:1.30                         # runtime (Debian)
```

- The composer stage installs `vendor/` so the Vite build can resolve the Livewire ESM
  module. `--no-dev --no-scripts` because there's no full app/artisan here.
- The builder stage does `npm ci --no-audit --no-fund` then `npm run build` — same pins as
  the php-fpm build, so both images contain identical hashed assets.
- The runtime image gets the vhost (`/etc/nginx/conf.d/default.conf`, auto-included by the
  nginx image), the built `public/` tree, and `curl` (`apt-get`, used only by the
  healthcheck — the Debian nginx image has no `wget`). `CMD ["nginx", "-g", "daemon off;"]`
  keeps the container alive.

### `docker/production/nginx/conf.d/default.conf`

The single vhost. Facts worth knowing:

- `listen 80` / `listen [::]:80` — LAN HTTP. TLS would change this (see Deferred).
- `server_name _` — catch-all on our private edge; safe here.
- `root /var/www/public`, `index index.php`, `charset utf-8`.
- `server_tokens off` — nginx version not leaked in `Server` header (verified live).
- **gzip**: on, level 6, min 1024 bytes, for text/JSON/JS/CSS/SVG/font types; Vite output
  is not precompressed, so this buys real transfer savings on the LAN.
- **Security headers**: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`,
  `Permissions-Policy`. Caution: a `location`-level `add_header` replaces server-level
  inheritance entirely — hence the `/build` location re-declares them.
- `location /` → `try_files $uri $uri/ /index.php?$query_string` (SPA fallback for
  routes).
- `location /build` → serves the content-hashed Vite assets with
  `Cache-Control: public, max-age=31536000, immutable` (one year, no revalidation —
  hashes change per release) and re-declared security headers. Access logs off.
- `error_page 404 /index.php` → Laravel renders fancy 404 pages.
- The `index.php` location: `fastcgi_pass php-fpm:9000`, `SCRIPT_FILENAME
  $realpath_root$fastcgi_script_name` (canonical Laravel fastcgi config), sets
  `HTTP_X_FORWARDED_FOR $remote_addr` and `HTTP_X_FORWARDED_PROTO $scheme` so the app can
  trust proxies (see `bootstrap/app.php`); bumps fastcgi buffers for fpm responses;
  `fastcgi_hide_header X-Powered-By`.
- `location ~ /\.(?!well-known).*` → `deny all` — dotfiles (like a stray `.env` left in
  `public/`) are not served. Verified live: `GET /.env` → 403.

### `docker/production/php-fpm/entrypoint.sh`

Runs as root before `USER www-data` isn't relevant — the entrypoint actually runs as the
image `USER` (`www-data`) since it's the process entrypoint; setup needs only app
writability, which `www-data` has. Script:

```bash
#!/bin/bash
set -e

raw_key="${APP_KEY#base64:}"

if [ -z "${APP_KEY}" ] || [[ "${APP_KEY}" != base64:* ]] || [ "${#raw_key}" -ne 44 ] || [ "$(printf '%s' "${raw_key}" | base64 -d | wc -c)" -ne 32 ]; then
    echo "ERROR: APP_KEY is missing or in a bad format (expected: base64: + a 32-byte key). Generate a key and put it in .env, e.g. printf 'base64:%s\n' \"\$(openssl rand -base64 32)\"" >&2
    exit 1
fi

php artisan migrate --force
php artisan optimize

exec php-fpm
```

- One guard, one message. It catches empty, a missing `base64:` prefix, wrong length,
  invalid base64, and keys that don't decode to exactly 32 bytes (AES-256-CBC) in a single
  condition — e.g. pasting a bare `openssl rand -base64 32` string without the prefix.
- The guard catches a bad key fast instead of Laravel silently failing at first encrypt.
- The guard only **reports** the problem — it cannot fix it from inside the container.
  Without `APP_KEY` the cached config is broken and `artisan` itself fails to run, and
  there is no writeable `.env` to write a key into anyway. The fix is done on the user's
  own machine (generate a key locally — e.g. `printf 'base64:%s\n' "$(openssl rand -base64 32)"`;
  paste it into `.env`, the value must start with `base64:`) and is taught in the
  deployment runbook below.
- `migrate --force` every boot: idempotent (migrations table tracks ran migrations) and
  Compose guarantees MySQL is healthy first. **Not** `--seed`: `UserSeeder` seeds a known
  developer account with a public password — production should be seeded by hand or not at
  all. The audit confirmed no seeding is needed for a working app.
- `php artisan optimize` bakes config/routes/views/event caches into
  `bootstrap/cache`, so each boot picks up `.env` changes (this is the mechanism that makes
  "edit `.env`, recreate the php-fpm container" sufficient — see the env-change workflow).
- `exec php-fpm` hands the PID to PHP-FPM (pid 1 = php-fpm; signals work, no orphaned
  wrapper).
- The container clock runs UTC; log timestamps reflect that.

### `.dockerignore`

Build-context filter (43 lines, comment-free now). Keep list, in order:

```
/node_modules
/vendor
.git
.gitattributes
.gitignore
.env
.env.*
.ai
README.md
/tests
/scripts
/phpunit.xml
.editorconfig
.idea
.vscode
.zed
.nova
.codex
.cursor
.phpactor.json
.phpunit.result.cache
.phpunit.cache
_ide_helper.php
_ide_helper_models.php
.DS_Store
Thumbs.db
*.log
docker-compose.yml
Dockerfile
.dockerignore
```

Effects worth knowing:

- **Never** ships `.env` or `.env.*` into an image — secrets only arrive via `env_file`.
- `.ai/` (this whole skill tree), `tests/`, `scripts/` stay in the repo but never touch a
  container.
- The writable-dir marker files (`.gitignore` files under `storage/framework/...`, added in
  `storage/framework/views/.gitignore`) ARE included — that's deliberate: the image ships
  the storage skeleton so the named volume initializes with `www-data` ownership and the app
  can write at runtime.
- Verified side effect: the production build context is exactly the whitelisted file set.

### `laravel/.env.production`

Committed template, source of truth for fresh deployments. Content (prod values already
baked in; secrets are blank):

```ini
APP_NAME=Planner
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

APP_LOCALE=fa
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_THEME=ocean

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=planner
DB_USERNAME=planner
# Required — app DB user password (MYSQL_PASSWORD) and MySQL root password.
DB_PASSWORD=
MYSQL_ROOT_PASSWORD=

SESSION_DRIVER=redis
SESSION_CONNECTION=session
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_EXPIRE_ON_CLOSE=false

CACHE_STORE=redis

REDIS_CLIENT=phpredis
REDIS_HOST=redis
# Required — Redis requirepass.
REDIS_PASSWORD=
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_CACHE_LOCK_CONNECTION=cache
```

Semantics and decisions (per group):

- **APP_***: `fa` locale, `ocean` theme, `BCRYPT_ROUNDS=12` (12 is Laravel's default
  work factor; kept explicit for documentation). `APP_KEY` is blank on purpose — it must be
  generated per deployment (see runbook).
- **LOG_***: `stack` → `single`, info level, stderr in the container (docker log driver).
- **DB_***: host is `mysql` (docker DNS). The non-root user `planner` is created by MySQL
  on first init from `MYSQL_DATABASE/USER/PASSWORD`. `MYSQL_ROOT_PASSWORD` is the host's
  MySQL root account, only used when the data dir is empty. **Caution**: after a volume is
  initialized, MySQL no longer re-reads `MYSQL_DATABASE/USER/PASSWORD` — keep them in sync
  with the actual DB user.
- **SESSION_***: driver `redis` on the dedicated `session` connection (DB 2, added in
  `config/database.php`), encrypted, expire-on-close false (a real persistent login cookie;
  the session itself is duration-limited by `SESSION_LIFETIME=120` minutes of inactivity).
  `SESSION_SECURE_COOKIE` is unset (plain HTTP — see Deferred).
- **CACHE_***: cache in redis (DB 1). `REDIS_CACHE_LOCK_CONNECTION=cache` makes Laravel's
  cache-based locks (used by `Route::block`) use the cache redis connection too.
- **REDIS_***: `phpredis` client, host `redis`, `REDIS_PASSWORD` empty by default — set it
  on deployment so Redis has `requirepass` (the compose `command` wires this up).

Note the "service name = host" pattern everywhere: `DB_HOST=mysql`, `REDIS_HOST=redis` —
these are only valid inside `planner_network`; the app never talks to host-local services.

### APP-SIDE changes for this deployment

These ship with the app; `docker/` alone is not enough.

#### `bootstrap/app.php`

```php
$middleware->trustProxies(at: '*');
$middleware->redirectGuestsTo('/login');
$middleware->redirectUsersTo('/dashboard');
```

- `trustProxies(at: '*')` — the only thing in front of the app is our nginx, which sets
  `X-Forwarded-For`/`X-Forwarded-Proto` for every request, so trusting all proxies is safe
  here and enables correct scheme/host resolution (SSL through the edge, afterwards).
- Guest/authenticated redirects to `/login` and `/dashboard` (the app has no other
  entrypoints worth guessing).

#### `config/database.php` — added redis `session` connection

```php
'session' => [
    'url' => env('REDIS_URL'),
    'host' => env('REDIS_HOST', '127.0.0.1'),
    'username' => env('REDIS_USERNAME'),
    'password' => env('REDIS_PASSWORD'),
    'port' => env('REDIS_PORT', '6379'),
    'database' => env('REDIS_SESSION_DB', '2'),
    'max_retries' => env('REDIS_MAX_RETRIES', 3),
    ... (same backoff block as the cache connection)
],
```

So DB 2 = sessions, DB 1 = cache (already present), DB 0 = default/anything else — they
share the single Redis instance but are key-space isolated.

#### Route blocking: `routes/web.php` + `app/Providers/AppServiceProvider.php`

- Every route and the Livewire update endpoint are declared with `->block(10, 10)`
  (10 s timeout, 10 s lock): while one request holds the session's Redis lock a duplicate
  overlapping request for the same session waits instead of racing. This is the app-side
  complement to Redis sessions — it protects rapid double-submits / back-button retries from
  writing twice, and it uses the cache lock connection (`REDIS_CACHE_LOCK_CONNECTION=cache`)
  so locks live on Redis.
- `AppServiceProvider::boot()` overrides Laravel's Livewire update route:
  `Route::post($path, $handle)->middleware(['web', RequireLivewireHeaders::class])->name('livewire.update')->block(10, 10);`
  — applies the same block behavior to the Livewire request channel (the default route is
  registered by Livewire itself; this redeclaration is the mechanism to add `block`).

Implemented solely because sessions moved to a durable store (Redis) where stale
concurrent writes are a real risk; with the old DB/file sessions in dev this would not
matter.

#### `.gitignore`, `composer.json`, `storage/framework/views/.gitignore`

- `.gitignore` un-ignores `laravel/.env.production` (it is committed; `.env`/`.env.backup` stay
  ignored) and replaces the blanket `/storage/framework` ignore with per-directory
  `.gitignore` files — this keeps the storage skeleton in git (and therefore in the image,
  so the named volume auto-populates with www-data ownership). `storage/framework/views/
  .gitignore` was added as part of that. (The `composer.json` `setup`/`post-root-package-
  install` wiring is part of the dev model — see `development.md`.)

---

## Environment Variables: Host → Container Mapping

| Compose file value | Host `.env` key it reads | Purpose |
|---|---|---|
| `env_file: .env` (php-fpm) | (all) | Every app var (`DB_*`, `REDIS_*`, `SESSION_*`, `APP_KEY`, ...) |
| `MYSQL_ROOT_PASSWORD` | `MYSQL_ROOT_PASSWORD` | MySQL `root` password (first init only) |
| `MYSQL_DATABASE` | `DB_DATABASE` | create DB `planner` on init |
| `MYSQL_USER` | `DB_USERNAME` | create user `planner` on init |
| `MYSQL_PASSWORD` | `DB_PASSWORD` | password for that user (init only) |
| `REDIS_PASSWORD` (redis `environment` + `$$REDIS_PASSWORD` in command) | `REDIS_PASSWORD` | `requirepass` for Redis |
| `REDISCLI_AUTH` (redis) | `REDIS_PASSWORD` | redis-cli reads it for healthcheck auth |
| `${VAR:?Set X in .env}` (APP_KEY, DB_PASSWORD, MYSQL_ROOT_PASSWORD, REDIS_PASSWORD) | same keys | required — Compose aborts immediately if any is empty |

Notes:

- Compose reads the top-level `.env` automatically for interpolation; the same `.env` is
  then handed to php-fpm via `env_file`.
- `$$` escapes a literal `$` from Compose interpolation so the *container* expands the var
  at runtime; plain `${VAR}` is interpolated by Compose at parse time. The redis command
  uses the former (`"exec redis-server --requirepass \"$$REDIS_PASSWORD\""`), the mysql
  healthcheck the latter (`"-p${MYSQL_ROOT_PASSWORD}"`). All healthchecks are exec-form
  `CMD` — no shell.
- **No `MYSQL_PWD` anywhere.** The mysql image's first-boot bootstrap connects to a
  temporary server whose `root` is passwordless on purpose; any `MYSQL_PWD` in the
  environment makes those connections (and therefore the whole init) fail with
  `Access denied (using password: YES)` — leaving root empty-password, the app user
  uncreated, and a half-initialized data dir. The container env only passes
  `MYSQL_ROOT_PASSWORD` for the *entrypoint*; the healthcheck gets it via Compose
  interpolation (`-p${MYSQL_ROOT_PASSWORD}`), not via the client's `MYSQL_PWD` auto-read.
- Sanity-checked at the audit: connecting with each password from `.env` works
  (planner user, root, PONG with pass / NOAUTH without).

---

## Decisions Log

Chronological; every significant choice with its why + the alternative considered.

1. **Multi-stage images, zero bind mounts.** The repo is the artifact; a fresh machine
   just builds. Alternative (bind-mount the code) was rejected: host paths, perms and host
   deps would leak in, and there'd be nothing to hand over.
2. **Build assets in BOTH images.** php-fpm must render `@vite` (needs `manifest.json`),
   nginx must serve the files. Rejected: a shared volume of assets — requires a "builder"
   one-shot container and ordering hacks; duplicating the build with identical pins is
   simpler and self-contained.
3. **Pinned, reproducible versions.** Tags: `composer:2.10`, `php:8.4-fpm`,
   `node:24`, `nginx:1.30`, `mysql:8.4`, `redis:8.10`. These are
   MAJOR.MINOR tags → patch updates come free with each rebuild; moving the minor is a
   deliberate act. Composer kept in sync across both images from the single `composer:2.10`
   image.
4. **No custom php.ini / pool conf.** The base `php:8.4-fpm` already ships production-sane
   defaults incl. opcache (verified: `docker-php-ext-opcache.ini` is loaded). A
   `20-status-path.conf` override we once added was removed as unnecessary.
5. **Redis for sessions + cache, MySQL for data.** Sessions are small, ephemeral and want
   speed; caching wants speed; the durable source of truth stays in MySQL (named volume).
   Redis runs without a volume (session/cache loss = forced re-login, acceptable).
6. **`phpredis` client** (PHP extension) — the config default; the alternative
   (`predis`) is a pure-PHP fallback not needed when we control the image.
7. **`SESSION_ENCRYPT=true`**, sessions encrypted at rest in Redis.
8. **`SESSION_EXPIRE_ON_CLOSE=false`** — a real (persistent) cookie; the browser keeps you
   logged in, the 120-minute *server-side* inactivity lifetime remains the real limit. The
   skeleton's `true` (cookie dies on browser close) was changed after deciding it made no
   sense for this app's usage.
9. **`Route::block()`, `Livewire::setUpdateRoute`** — serialize overlapping requests per
   session when sessions live in Redis (see APP-SIDE above).
10. **`trustProxies('*')`** + nginx rewriting `X-Forwarded-*` — correct behind our only
    proxy. Rejected: a specific proxy IP list (host's Docker bridge IP varies).
11. **Entrypoint guards `APP_KEY`**, runs `migrate --force` then `optimize` on every boot.
    Key rotation/`.env`-change flow relies on this (optimize re-bakes on boot).
12. **migrations only, never `--seed`** — `UserSeeder` is a known-credential dev seed
    (`amirgjz` / `12345678`, git-committed design); production users must set passwords
    themselves. (The seeder was refactored to `firstOrCreate` so re-seeding in dev doesn't
    duplicate rows.)
13. **Dedicated redis `session` connection (DB 2)** — key-space isolation from cache
    (DB 1) and default (DB 0); added to `config/database.php`. `REDIS_CACHE_LOCK_CONNECTION`
    points locks at the cache DB too.
14. **Single named network `network`** (→ `planner_network`). Only nginx publishes a port.
    MySQL/Redis are network-internal — bad things can't reach them from the host by
    accident.
15. **Named volumes** `database` + `storage` (→ `planner_database`, `planner_storage`).
    Everything else in the image is disposable and rebuilt. `bootstrap/cache` is NOT a
    volume: it's regenerated by `optimize` each boot.
16. **Log rotation** `json-file 10m × 3` everywhere. An unbounded `docker logs` would outgrow
    the disk on a long-running personal app.
17. **`restart: unless-stopped`** everywhere.
18. **Healthcheck chain** mysql/redis → php-fpm → nginx, plus `/up` for external probes.
19. **Static container names + `server_name _`** — predictable, small-scale. Not a
    multi-instance swarm setup (would need unique names/`--scale`).
20. **`server_tokens off`, security headers, dotfile-deny, `/build` immutable cache,
    fastcgi niceties** — small, verified live, cheap hardening for an edge that could
    accidentally be exposed someday.
21. **`laravel/.env.production` committed; `.env` ignored.** A fresh user copies the template.
    The dev template and its `composer.json` wiring are documented in `development.md`.
22. **`unzip` in the builder.** Needed to unpack Composer `--prefer-dist` dependency
    archives and PECL tarballs. The `curl` leftover from the removed Composer curl-installer
    was removed on this rebuild (verified: nothing in the build uses it).
23. **App-level redirects** (`/login` guest, `/dashboard` auth) — single clear entry point.
24. **Enforce secrets at Compose level** via `${VAR:?msg}` for `APP_KEY`, `DB_PASSWORD`,
    `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD`. A fresh `.env` now can't boot insecurely;
    Compose aborts immediately with the name of the missing var. Replaced the old
    `:-root`/`:-` defaults which silently fell back to weak or empty passwords. The
    entrypoint additionally validates `APP_KEY` is a `base64:`-prefixed key decoding to
    exactly 32 bytes (AES-256-CBC), catching the common mistake of pasting a bare
    `openssl rand -base64 32` output.
25. **MySQL healthcheck `start_period: 30s`.** First boot initializes the data dir and is
    slow; without it MySQL flips to "unhealthy" during init and delays the whole chain.
26. **Production stage keeps `-dev` libs** (not the smaller non-dev variants). The
    `php:8.4-fpm` tag is a floating major.minor — its base Debian release can change across
    rebuilds (bookworm → trixie shifts `libzip4`→`libzip5`, `libicu72`→`libicu76`, etc.).
    `-dev` names are stable across that; the runtime names are not. Slimming the image
    isn't worth that build fragility for this scale.
27. **`npm ci --no-audit --no-fund`** — quiet and fast; harmless because the lockfile is
    the source of truth and the rebuild already ignores registry metadata.
28. **All-Debian, no-suffix base images.** `node:24`, `nginx:1.30`, `redis:8.10` (were
    `-alpine`) so every image uses glibc and `apt`. Chosen for consistency and the boring
    default; costs only image size/disk (runtime is unaffected). All-Alpine was never
    possible — `mysql:8.4` has no official Alpine variant, and its no-suffix tag is Debian
    anyway. Making every image no-suffix yields one distro everywhere for free. The Debian
    nginx image ships no `wget`, so the nginx runtime now installs `curl` for its healthcheck.
29. **Healthchecks real, not just "alive".** MySQL switched `mysqladmin ping` (which returns
    0 even on failed auth) for a real `mysql -e 'SELECT 1'` round-trip. nginx uses
    `curl -fs /up` (`-f` fails on any non-2xx). Redis checks via plain `ping`. All four
    healthchecks are exec-form `CMD` — no shell. Auth is passed without dedicated
    `-p`/`-a` **flags on the command line at check time** where possible: redis is authed
    via `REDISCLI_AUTH` env; mysql gets its password interpolated by Compose at parse time
    (`-p${MYSQL_ROOT_PASSWORD}`). **Not `MYSQL_PWD`** — an env var by that name breaks the
    image's first-boot init outright (see the notes above), so the root password never
    rides the client's `MYSQL_PWD` auto-read.

---

## Deployment Runbook (fresh machine, golden path)

Prerequisites: Docker Engine with Compose v2 (Docker ≥ 24) and git.

```bash
git clone <repo-url> planner
cd planner

cp laravel/.env.production laravel/.env   # source of truth template
```

Fill `laravel/.env` — four required values:

```bash
APP_KEY=$(printf 'base64:%s' "$(openssl rand -base64 32)")  # 32-byte, base64: prefix — do NOT reuse from elsewhere
# in the file, set strong values for:
DB_PASSWORD=...                          # app DB user password
MYSQL_ROOT_PASSWORD=...                  # host mysql root password (first init only)
REDIS_PASSWORD=...                       # redis requirepass
```

Then:

```bash
./planner up                            # builds on first run; or the raw form:
docker compose -f laravel/compose.production.yml up -d --build
./planner ps                            # or: docker compose -f laravel/compose.production.yml ps
curl -s http://localhost/up              # expect 200 (Laravel health route)
```

First run needs internet once (base images + composer/npm downloads); later runs reuse
the Docker layer cache.

Verification on first boot:

```bash
# stack health
docker compose -f laravel/compose.production.yml ps                    # all "healthy" (php-fpm/nginx) or "running"
# app is real on 80
curl -I http://localhost                                   # nginx served, Server header has no nginx version
# logs are sane
docker compose -f laravel/compose.production.yml logs --tail=50 php-fpm # no ERRORS; migration ran
```

If the migration failed, the php-fpm container will be restarting and `/up` won't answer —
see the startup note in Verification/Troubleshooting.

---

## Day-to-Day Operations

| Task | Command |
|---|---|
| Status | `docker compose -f laravel/compose.production.yml ps` |
| Logs (follow) | `docker compose -f laravel/compose.production.yml logs -f <service>` |
| Apply app changes (after `git pull`) | `docker compose -f laravel/compose.production.yml up -d --build` |
| Apply only env change | edit `.env` → `docker compose -f laravel/compose.production.yml up -d` (recreates affected containers; the entrypoint re-bakes config) |
| Force recreate everything | `docker compose -f laravel/compose.production.yml up -d --force-recreate` |
| Stop everything | `docker compose -f laravel/compose.production.yml down` (volumes survive) |
| **Never** | `down -v` / `docker volume rm planner_database planner_storage` on a live install — this is the whole database |
| Generate a fresh APP_KEY | on your own machine: `printf 'base64:%s\n' "$(openssl rand -base64 32)"`, then paste into `.env` (must start with `base64:`) |
| Shell into a service | `docker compose -f laravel/compose.production.yml exec php-fpm sh` (runs as `www-data`) |
| Manual DB backup (recommended, no automation yet) | `docker compose -f laravel/compose.production.yml exec mysql sh -c 'mysqldump -u planner -p"$MYSQL_PASSWORD" planner' > backup.sql` |

APP_KEY rotation procedure (rare; forces everyone to re-login):
1. Generate a new key on your own machine: `printf 'base64:%s\n' "$(openssl rand -base64 32)"`.
2. Edit `.env`, then `up -d --force-recreate php-fpm nginx` (php-fpm re-bakes config from
   the key; sessions/cookies are invalidated on next access).
3. Verify `/up` and a fresh login.

### Env-change workflow (why it works)

Compose re-reads `.env` on **every** compose command (interpolation + `env_file`), but a
running container freezes its environment at start. So: change `.env` → recreate the
container that needs it. php-fpm's entrypoint runs `optimize` on boot, so the cached
config (which embeds env values) is rebuilt automatically and needs no manual
`config:clear`. `app-key` changes likewise require a php-fpm recreate only.

---

## Verification & Troubleshooting

| Symptom | Check | Fix |
|---|---|---|
| `docker compose up` aborts "required variable … missing a value" | `.env` has blank `APP_KEY` / `DB_PASSWORD` / `MYSQL_ROOT_PASSWORD` / `REDIS_PASSWORD` | fill the named var in `.env` |
| `php artisan key:generate` or `up` throws "Unsupported cipher or incorrect key length" | `APP_KEY` is bare base64 without `base64:` prefix | regenerate: `printf 'base64:%s\n' "$(openssl rand -base64 32)"`, paste into `.env`, `up -d --force-recreate php-fpm` |
| `502 Bad Gateway` | is php-fpm healthy? | `docker compose -f laravel/compose.production.yml up -d php-fpm`; check `logs php-fpm` |
| `/up` 500 / "whoops" | php-fpm logs | app exception; check memory, APP_KEY validity |
| Container restart-loop | `logs` shows migration error | mysql not ready (wait, `start_period` covers first boot) or DB credentials wrong |
| MySQL won't accept new creds | volume already initialized | MySQL ignores env after init; fix the DB user instead (or recreate volume deliberately) |
| Redis NOAUTH errors | `.env` REDIS_PASSWORD vs container env | edit `.env`, recreate redis + php-fpm |
| Everything healthy but login fails | session store | clear: `exec redis redis-cli -a "$REDIS_PASSWORD" FLUSHALL` (nukes sessions+cache; data intact) |
| Port 80 busy on host | `ss -ltnp` | stop the other process, or map another port in compose |

Healthcheck definitions (for reference when diagnosing the chain):

| Service | Test | interval/timeout/retries/start |
|---|---|---|
| php-fpm | `php -r` fsockopen TCP 127.0.0.1:9000 | 10s/3s/5/30s |
| nginx | `curl -fs http://127.0.0.1/up` (full stack) | 30s/3s/3/5s |
| mysql | `mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -e SELECT 1` (exec CMD; real auth) | 10s/5s/5/30s |
| redis | `redis-cli ping` (auth via `REDISCLI_AUTH`) | 10s/5s/5 |

`depends_on: condition: service_healthy` orders them: mysql+redis → php-fpm → nginx.

---

## Docker Hygiene on This Host

The whole dev-era image zoo was cleaned: leftover base images (php:8.3-cli-alpine,
postgres:17-alpine, old nginx:alpine/php:8.4-fpm/osx leftovers), 16 orphan volumes
(13 anonymous + 3 stale named: `planner_laravel-storage-production`,
`planner_mysql-data-production`, `planner_planner-database`), builder cache, dangling
images. Current expected host state ≤ ~10-11 images (4 project/prod + build bases pulled on
demand), 2 named volumes, 0 dangling images.

Rules forever:

- **Never** `docker system prune -a --volumes`, `docker volume prune`, or `down -v` on a
  live install — those delete `planner_database`/`planner_storage`.
- Safe weekly-ish hygiene:
  `docker system prune -f` (dangling images) and `docker builder prune -f`.
- Build bases (`php:8.4-fpm`, `composer:2.10`, `node:24`, `nginx:1.30`,
  `mysql:8.4`, `redis:8.10`, plus pulled tags) are ephemeral — deleting them is
  fine; the next `up --build` re-pulls automatically.
- Check footprint: `docker system df`.

---

## Future Note — Keeping This Setup Updated with the Project

This is a living document of *how the Docker prod setup stays in sync with the project's
growth*. It is deliberately not about TLS/backups/multi-node (those are recorded once in
the [Deferred section](#explicitly-deferred-and-not-done) below).

### The one structural truth

**The images are immutable.** The repo (locks + pins) is what defines a deployment. Every
update in this document is just "change the pins/files, rebuild, verify."

### Categories of update

1. **App code / config changes** → `git pull && docker compose -f laravel/compose.production.yml
   up -d --build && curl -s http://localhost/up` (expect 200) + one smoke login. Both
   images rebuild from the same context/locks, so no version skew.
2. **Dependency bumps** (`composer.lock`, `package-lock.json`): bump them in dev
   (`composer update`, `npm update`) while the app is tested, commit the two lockfiles,
   then rebuild. Never hand-edit locks in prod; never `up --build` with a dirty lock.
3. **Framework upgrade** (e.g. Laravel 13.x → 14.x when released): treat like a
   dependency bump but ALSO re-check `bootstrap/app.php`/`config/database.php` against the
   new skeleton and re-run the prod audit (config defaults vs `laravel/.env.production`).
4. **Base-image patch updates (free)**: our tags are MAJOR.MINOR, so a rebuild silently
   pulls the latest patch (php:8.4-fpm, nginx:1.30, mysql:8.4, redis:8.10,
   node:24). If a security advisory appears for a series, just `up -d --build` to
   update the patch and re-verify health.
5. **Base-image MINOR updates (deliberate)**: when moving to a new tag line (PHP 8.5,
   nginx 1.31, MySQL 9, Redis 9, Node 26 LTS, Composer 2.11): bump the tag in BOTH
   Dockerfiles / compose, rebuild, and run the full verification (health + `/up` + a login +
   a Livewire interaction). Update the version table at the top of this doc and the
   [Decisions Log](#decisions-log).
6. **Env surface changes**: any new/renamed env var the app reads must be added to
   `laravel/.env.production` (the committed source of truth) AND mentioned in a new Decisions Log
   row. Existing live installs need the same manual edit (copy from the template), then the
   recreate flow. If only the template changed, no rebuild is needed — but without updating
   live `.env` files nothing changes at runtime.
7. **Adding infrastructure when the app grows** — copy this project's established pattern:
   - Queue job processing → `composer` stage + a worker image, `QUEUE_CONNECTION=database`
     (already set) + `php artisan queue:work`, wired into `depends_on`/health as above,
     same logging/restart model. Do not add without an actual queue consuming feature.
   - **Redis persistence — required before any Redis-backed queue.** Today Redis has no
     volume and is intentionally ephemeral (sessions/cache only; losing them just forces a
     re-login). But `down` or `up -d --build` **recreates the container and erases its
     data**, so queued jobs in Redis would be lost. If the queue is ever switched to
     `QUEUE_CONNECTION=redis`, first give the `redis` service a named volume (same pattern
     as `database`) and enable RDB/AOF persistence, then add the volume to the snapshot
     table and Decisions Log. `stop`/`start` do not wipe Redis — only container
     recreation/removal does.
   - Scheduler → a `php artisan schedule:work` sidecar with the same convention.
   - A second VM / scale-out → would require shared redis/MySQL and a load balancer in
     front; that's the moment TLS/domain work begins too. Out of current scope.
8. **Editing `docker/` files**: any change here must (a) keep `.dockerignore`, `.env.*`
   templates, both Dockerfiles and `default.conf` coherent across the two images, (b) be
   rebuilt + health-verified, and (c) update this document (file-by-file, version table,
   decisions log). The comment-free code style in the images is intentional — the "why" for
   every line lives *here*, not in the Dockerfiles.
9. **Node 24 → next LTS**: when the active LTS moves and/or the toolchain demands it,
   bump `node:24` in both Dockerfiles at once (both images build identical assets
   today, keep it that way).
10. **Composer skew guard**: both images must keep using the same `composer:2.10` base —
    the sync guarantee comes from that single source.

### Anti-patterns to avoid

- Bind-mounting code "just for prod host edits" — breaks the immutable-artifact model.
- `docker compose` on the `laravel/compose.development.yml`/dev stack inside the production network.
- Editing `laravel/.env.production` and assuming live installs see it (they only see their `.env`).
- `up -d --build` while the working tree is dirty (unknown context → unknown image).

---

## Explicitly Deferred (and Not Done)

Recorded once for the record so future-me knows these were conscious decisions, not
oversights:

- **TLS/HTTPS**: no domain, LAN-local; serving plain HTTP. When TLS appears: nginx conf
  gains `listen 443` + cert lines + port `443:443` in compose, and
  `SESSION_SECURE_COOKIE=true` (or `null` behind a trusted TLS proxy like Cloudflare) in
  `laravel/.env.production`.
- **Automated backups**: none. The manual one-liner is in Day-to-Day Operations. Accepting
  the risk (personal LAN app, small data).
- **Multi-instance scaling / load balancing / monitoring**: single host; healthchecks are
  the only oversight today.
- **Rate limiting / brute-force login throttling beyond Laravel's defaults** (the
  `block()` work is about write races and session lock, not abuse).
- **CDN / compression pre-bake / Redis persistence**: unnecessary or contrary to the
  current size.
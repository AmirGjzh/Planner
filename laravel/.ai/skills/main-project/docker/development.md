# Docker Development Setup

> Snapshot documentation. Read this before touching anything under `docker/`,
> `laravel/compose.development.yml`, or the `.env.*` templates.

## Purpose and Audience

This document is the single source of truth for the Docker **development** setup of the
Planner application. It exists (1) so a future "me" — potentially two years from now — can
recall every decision, file, and workflow without re-deriving anything, and (2) so agents /
LLMs can work on this setup without needing to re-discover the architecture.

It is **not** user documentation. The end user never sees this file.

It is the dev companion to `production.md` — read that one for the deployed stack. This
file only covers the local development workflow.

---

## Current Status (snapshot)

| Item | Value |
|---|---|
| Dev model | Clone → copy template → fill `.env` → `up -d --build` (deps self-install on start) → `exec workspace sh` |
| Orchestration | Docker Compose v2 (`laravel/compose.development.yml`), project name `planner-development` |
| App | Laravel 13.x + Livewire 4.x (see `composer.json`) |
| Workspace image | `php:8.4-cli` (Debian/glibc) built from `docker/development/php-cli/Dockerfile` |
| Composer image (build-only) | `composer:2.10` |
| Node image (build-only) | `node:24` (no suffix — exact tag prod builds with) |
| MySQL image | `mysql:8.4` (same as prod) |
| Redis image | `redis:8.10` (same as prod) |
| Containers | `planner-development-workspace`, `planner-development-mysql`, `planner-development-redis` |
| Named volumes | `planner-development_database` (DB only; workspace is stateless) |
| Network | single user network `network` → prefixed `planner-development_network` |
| Host ports | `8000` (artisan serve), `5173` (Vite HMR) — MySQL and Redis have **no** host port |
| PHP extensions | `pdo_mysql`, `pdo_sqlite`, `intl`, `mbstring`, `zip`, `bcmath`, `gd`, `redis` |
| Redis | present (`redis:8.10`, same image + `requirepass` as prod; sessions/cache drivers) |
| Tests | Pest on SQLite `:memory:` (via `phpunit.xml`) — needs `pdo_sqlite` |
| Restart policy | `unless-stopped` on all services |

Runtime state at the time of writing: the dev stack was running and the rebuilt
workspace image (node/composer/php) verified working by the user.

---

## Target Dev Model

- **One container = one dev terminal with everything inside**: PHP CLI + Composer + Node
  (npm/npx). Run Laravel with `artisan serve`, Vite with `npm run dev`, migrations, tests
  and Tinker — all from `<project>/docker`-provided tools.
- **No nginx, no php-fpm in dev.** Production-service complexity is deferred. Redis *is*
  present to mirror prod's session/cache stack (see below).
- The project folder is **bind-mounted** `.` → `/var/www`, so every file you edit shows up
  in the container live (and vice versa). No image rebuild for app-code changes.
- The container runs as **your host UID/GID** (default `1000:1000`), so files created in
  the container are owned by your host user — no permission fights on the mount.
- MySQL is isolated inside the dev network (container name `mysql`), like prod. Dev DB
  data lives in its own volume and survives restarts.
- The repo is the deliverable: a fresh machine needs a clone, the filled-in `.env`, and
  `up -d --build`; you then install deps once (`composer install` / `npm install` inside
  the workspace — nothing runs automatically, see below) and start the two dev servers.

---

## Architecture

```
        Host
  ┌──────────────┬───────────────────────────────┐
  │  :8000  ◄────┘    :5173                      │
  ▼                                              │
 planner-development-workspace ◄── bind mount ./:/var/www │
 ┌─────────────────────┐                         │
 │ php:8.4-cli          │  artisan serve 0.0.0.0:8000
 │ + composer           │  Vite dev   0.0.0.0:5173
 │ + node / npm / npx   │  ──► mysql  (container DNS, :3306)
 │ + redis (ext)        │  ──► redis  (container DNS, :6379)
 └──────────┬───────────┘
            │ TCP:3306, :6379 (network only — no host ports)
            ▼
 planner-development-mysql (mysql:8.4, data in planner-development_database)
 planner-development-redis (redis:8.10)
```

Key facts:

- Containers reach each other by **container name as DNS** inside the dev network:
  `mysql` is the DB host (matches `DB_HOST=mysql`) and `redis` is the cache/session host
  (matches `REDIS_HOST=redis`) in `laravel/.env.development`.
- **Only the workspace publishes host ports** (8000, 5173). MySQL and Redis are never
  exposed to the host — no GUI access by default (see [Future Notes](#future-notes-desires-and-not-done)).
- The workspace is **stateless**: all project files come from the bind mount; the image
  itself only carries the toolchain (~php + composer + node). `vendor/` and `node_modules/`
  live on the host and are shared through the mount.
- `CMD ["sleep", "infinity"]` plus `tty: true` + `stdin_open: true` keep the workspace
  alive so you can `exec` into it at any time. There is **no entrypoint** — the image runs
  only its `CMD`, so *nothing* runs automatically: no installs, no setup. You install deps
  and run servers yourself, on the bind mount (which is what keeps them persistent).

---

## Repository Layout (Docker Parts)

```
/                                repo root (docker build context)
├── planner                              commands: ./planner up, ps, build, ...
├── planner-dev                          commands: ./planner-dev shell, up, build, ...
├── docker/
│   ├── development/
│   │   └── php-cli/Dockerfile           dev toolchain image
│   └── production/                      prod images (see production.md)
├── laravel/                             app code (bind-mounted into workspace)
│   ├── compose.development.yml          dev stack definition
│   ├── compose.production.yml           prod stack definition (see production.md)
│   ├── .env.development                 committed dev template (source of truth)
│   ├── .env                             real values, git-ignored, copied from a template
│   ├── vite.config.js                   dev server host / HMR / polling (see below)
│   ├── composer.json                    `dev` / `test` / `setup` scripts
│   ├── phpunit.xml                      test DB = sqlite `:memory:`
│   └── .ai/skills/main-project/docker/ production.md (prod) + this file
```

---

## File-by-File

### `laravel/compose.development.yml`

Single-file Compose v2 spec, project name `planner-development`. The full file:

```yaml
name: planner-development

services:
  workspace:
    container_name: planner-development-workspace
    build:
      context: ..
      dockerfile: ./docker/development/php-cli/Dockerfile
    restart: unless-stopped
    user: "${UID:-1000}:${GID:-1000}"
    tty: true
    stdin_open: true
    working_dir: /var/www
    env_file:
      - .env
    environment:
      APP_KEY: ${APP_KEY:?Set APP_KEY in .env}
    volumes:
      - .:/var/www
    ports:
      - "8000:8000"
      - "5173:5173"
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

  mysql:
    container_name: planner-development-mysql
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
    container_name: planner-development-redis
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
```

Notes:

- `user: "${UID:-1000}:${GID:-1000}"` — the workspace runs as your uid/gid. On Linux,
  `UID`/`GID` are exported by most shells; if not, the fallback `1000:1000` is typical.
- The secrets are required via `${VAR:?err}` — Compose aborts naming the missing one, same
  contract as prod: the workspace enforces `APP_KEY`, mysql enforces `MYSQL_ROOT_PASSWORD`
  /`DB_PASSWORD`, redis enforces `REDIS_PASSWORD`. `.env` is never committed; values live
  only in the local file.
- The workspace waits for **both** `mysql` and `redis` to pass their healthchecks.
  Both are exec-form `CMD` (no shell): `mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -e
  'SELECT 1'` — the password is interpolated by Compose at parse time — and `redis-cli
  ping`, authed via `REDISCLI_AUTH` — the exact prod pattern.
- **`MYSQL_PWD` is deliberately NOT set on mysql.** The image's first-boot bootstrap
  connects to a temporary server whose `root` is still passwordless; a stray `MYSQL_PWD`
  injects a password into every such connection and the entrypoint's own `ALTER USER` /
  `CREATE USER` abort with `Access denied (using password: YES)`, leaving root empty and
  the app user uncreated (half-initialized data dir). `MYSQL_ROOT_PASSWORD` is passed for
  the *entrypoint*, never via the client's `MYSQL_PWD` auto-read.
- No `entrypoint:` / `command:` override in compose — and **no `ENTRYPOINT` in the image at
  all** (deliberately, see below). The workspace just sleeps; `tty`/`stdin_open` keep it
  interactive, and you run everything yourself.

### `docker/development/php-cli/Dockerfile`

The entire dev toolchain in one **Debian-based** image. Full file:

```dockerfile
FROM node:24 AS node

FROM composer:2.10 AS composer

FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    git \
    ca-certificates \
    libonig-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_sqlite \
    intl \
    mbstring \
    zip \
    bcmath \
    gd \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && node --version \
    && npm --version \
    && composer --version

RUN mkdir -p /home/workspace && chown -R 1000:1000 /home/workspace && php -m | grep -q redis

ENV HOME=/home/workspace

WORKDIR /var/www

CMD ["sleep", "infinity"]
```

Why it looks like this:

- **Debian (`php:8.4-cli`), not Alpine.** Node's official Linux binaries are musl-linked
  in the alpine builds but glibc-linked in the Debian builds; they can't run in a glibc
  PHP image. `node:24` is glibc and the **exact tag prod builds with** (all-Debian, no
  suffix, repo-wide).
- **Composer is a PHP phar** — copying `/usr/bin/composer` from `composer:2.10` is fully
  portable and avoids a second install method. Same image the prod stack builds with.
- **Node + npm come from the `node:24` stage**, not a NodeSource script.
  The earlier draft ran the NodeSource install script (pulled in Python/pip cruft); the
  two-stage copy is smaller and version-pinned.
- **`npm`/`npx` are symlinks** into the copied `npm` package on `/usr/local/bin`.
- **`pdo_sqlite` + `libsqlite3-dev`** are required for Pest (`phpunit.xml` uses SQLite
  `:memory:`). Tests run inside this container; prod does *not* need pdo_sqlite.
- **`redis` extension via `pecl`** — the same line prod's builder image uses. Dev now runs
  `SESSION_DRIVER=redis` / `CACHE_STORE=redis` (see `laravel/.env.development`), so the workspace can
  talk to the dev Redis server.
- **`curl` stays installed** — unlike prod (where we removed it; there it was only for the
  healthcheck), here it's a live developer tool in the terminal.
- **`HOME=/home/workspace`** (created and owned by `1000:1000`): npm/composer/git write
  their caches under `$HOME`. Without it the container tried `/.npm` (root-owned, EACCES —
  the exact failure seen during setup). Reading/npm/git caches now live in `/home/workspace`.
- The version guards (`node --version`, `npm --version`, `composer --version`, plus the
  `php -m | grep -q redis` extension check) run at build time so a broken copy fails the
  build instead of surprising you at runtime.
- **No entrypoint — nothing runs automatically.** The image ends at `CMD ["sleep",
  "infinity"]` and no `ENTRYPOINT` is set, so starting the container does *nothing* beyond
  keeping it alive. Installs, migrations, servers — all are manual (`composer install` +
  `npm install` once, on the bind mount; they persist there). This is deliberate: with the
  toolchain in the image and the app on the mount, there is nothing to bootstrap at boot,
  and no way for a script to surprise you.
- **Tools are images, never installed** (repo-wide rule, see Decision #14): PHP is the
  base image, composer + node/npm are build-stage copies, mysql/redis are services. The
  only things installed in-image are PHP runtime extensions and base utilities with no
  image equivalent (`curl`, `unzip`, `git`, `ca-certificates`). A future tool gets an
  official image or a stage — never an apt-get in a container.

### `laravel/.env.development`

Committed template — the dev source of truth. Deliberately mirrors the prod template's
philosophy: real values live only in the git-ignored `.env`.

```dotenv
APP_NAME=Planner
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_THEME=ocean

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=planner
DB_USERNAME=planner
DB_PASSWORD=
MYSQL_ROOT_PASSWORD=

SESSION_DRIVER=redis
SESSION_CONNECTION=session
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_EXPIRE_ON_CLOSE=false

CACHE_STORE=redis

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_CACHE_LOCK_CONNECTION=cache
```

Only four things are blank and must be filled in `.env`:
`APP_KEY`, `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD`. Everything else has a
config default or is unused in dev.

- `DB_HOST=mysql`, `REDIS_HOST=redis` — inside the dev network, service names are the
  hostnames.
- `SESSION_DRIVER=redis` / `CACHE_STORE=redis` — prod parity: dev exercises the *real*
  session/cache stack (Redis server + `phpredis` ext), not the database/store fakes.
- `SESSION_ENCRYPT=false` — the one deliberate divergence from prod's `true`, so dev tools
  can read sessions while you debug. Prod encrypts.
- Dev friendly: `APP_ENV=local`, `APP_DEBUG=true`, `LOG_LEVEL=debug`, and the SESSION/REDIS
  group mirrors `laravel/.env.production` exactly.
- Removed from stock Laravel: BROADCAST/QUEUE/FILESYSTEM/MEMCACHED/MAIL/AWS/VITE — each
  verified to have a sane config default or be unused in dev.

### `vite.config.js`

Three dev-server settings make Vite work from inside the container:

```js
server: {
    host: '0.0.0.0',          // reachable from the host via :5173
    hmr: {
        host: 'localhost',    // browser connects back to host port 5173
    },
    watch: {
        ignored: ['**/storage/framework/views/**'],
        usePolling: true,     // reliable file-watching on the bind mount
    },
},
```

These are the only container-specific bits — the plugins/inputs are unchanged.

### App-side wiring (read-only notes)

- `composer.json` scripts relevant in dev: `composer run dev` (concurrent: `artisan serve`
  + `queue:listen` + `npm run dev`), `composer test` (calls `artisan test` = Pest).
- **Why `--host=0.0.0.0`:** Docker forwards a published port to the container's *network
  interface* (eth0), not its loopback. `php artisan serve`'s default `127.0.0.1` binds
  only loopback, so the port mapping would reach nothing. The `dev` script passes
  `--host=0.0.0.0 --port=8000` explicitly (fixed — it now works), and the runbook uses the
  same explicit command. Vite needs no flag because `vite.config.js` forces `host: '0.0.0.0'`.
- `phpunit.xml` uses `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` — tests need no DB
  container and run anywhere with `pdo_sqlite`.

---

## Environment Variables: Host → Container

| Compose/dev file value | Source in `.env` | Notes |
|---|---|---|
| `workspace.env_file` | entire `.env` | all variables (APP_*, DB_*, SESSION_*, CACHE_*, REDIS_*) injected into the container |
| `workspace.environment.APP_KEY` | `${APP_KEY:?}` | compose-level guard mirroring prod's php-fpm — workspace won't start without it |
| `MYSQL_ROOT_PASSWORD` | `${MYSQL_ROOT_PASSWORD:?}` | required — bake at first `up`, changing later needs the volume reset (see Troubleshooting) |
| `MYSQL_DATABASE` | `${DB_DATABASE}` | must equal `DB_DATABASE` from the workspace's `.env` |
| `MYSQL_USER` | `${DB_USERNAME}` | ditto |
| `MYSQL_PASSWORD` | `${DB_PASSWORD:?}` | ditto |
| `REDIS_PASSWORD` | `${REDIS_PASSWORD:?}` | `requirepass` for the dev Redis |
| `REDISCLI_AUTH` | `${REDIS_PASSWORD:?}` | redis-cli auth used by the healthcheck (same as prod) |
| workspace port `8000` | — (static) | artisan serve |
| workspace port `5173` | — (static) | Vite HMR |

Rule of thumb: keep `DB_HOST=mysql`, `DB_PORT=3306`, `REDIS_HOST=redis`, `REDIS_PORT=6379`
and the four `DB_*`/`MYSQL_*`/`REDIS_*` credential values in sync between the two services —
Compose reads them from the *same* `.env`.

---

## Decisions Log

1. **One container with every tool** (`php-cli` + composer + node). Matches the stated
   goal "exec workspace sh → a terminal with everything"; avoids 3-4 tiny containers.
2. **Debian base, not Alpine.** Node alpine (musl) cannot run in a glibc PHP image.
   Staying Debian for PHP keeps the closest parity with prod (`php:8.4-fpm` is also
   Debian-based). Node 24 comes from the **glibc** `node:24` build — the exact tag prod
   uses, no suffix.
3. **Node via image stage, not NodeSource.** Smaller, pinned, no Python/pip baggage that
   the first draft's install script pulled in.
4. **Two-stage copying (composer phar + node)** instead of multi-exec installers. If any
   copy breaks, the build-time version guards fail loudly.
5. **No nginx/php-fpm in dev.** `artisan serve` is enough for local work. The prod stack's
   nginx/fpm (and its "assets built twice" dance) stay production-only.
6. **Redis in dev, for prod parity.** Same image (`redis:8.10`), same `requirepass` and
   healthcheck pattern as prod. Dev now uses the real drivers — `SESSION_DRIVER=redis`
   (`SESSION_CONNECTION=session`), `CACHE_STORE=redis` — and the `phpredis` extension in
   the workspace image. **`SESSION_ENCRYPT=false` in dev only**, so sessions stay readable
   for dev tools; prod keeps `true`.
7. **MySQL 8.4 same as prod**, but with its own dev volume and **no host port** — the
   database is only reachable from the workspace (matches prod's posture). Same for Redis:
   no host port in dev either.
8. **Bind mount `./:/var/www`** — code changes are live; the image is a toolchain, not an
   app artifact. Rebuild only when the *toolchain* changes.
9. **`user: ${UID:-1000}:${GID:-1000}` + `HOME=/home/workspace`** — cache dirs
   (`~/.npm`, `~/.composer`, `~/.git`) are writable and all container-created files belong
   to the host user. This fixed the real `EACCES /.npm` failure hit during setup.
10. **`tty`/`stdin_open` + `CMD sleep infinity`, no entrypoint.** Nothing runs at boot —
     no auto-install, no setup (the rule we keep to). Deps are installed once, manually,
     on the bind mount (`composer install` / `npm install`) and persist there; the image
     itself is just the toolchain.
11. **`pdo_sqlite` kept** so Pest (`sqlite :memory:`) works in the same container.
12. **`laravel/.env.development` = template with blank secrets**, same contract as the prod
    template: real values only ever live in the git-ignored `.env`.
13. **Prod-style cleanups carried over.** Exec-form `CMD` healthchecks with env/auth-passed
     credentials (`-p${MYSQL_ROOT_PASSWORD}` / `REDISCLI_AUTH`), `${VAR:?err}` secret
     enforcement (no `:-root` fallbacks), `start_period` on MySQL, and identical
     logging/restart policies — dev is as close to prod as it can be without hurting the
     developer. Note: mysql does **not** use `MYSQL_PWD`, because the mysql image's
     first-boot bootstrap connects to a passwordless temp-server root and any `MYSQL_PWD`
     in the environment makes those connections (and the whole init) fail.
14. **Tools are images, never installed (repo-wide rule).** PHP comes from the `php:8.4-cli`
    base image; composer and node/npm from `composer:2.10` / `node:24` build stages; mysql
    and redis from their official service images. The only things installed into a
    container are PHP runtime extensions and base utilities with no image equivalent
    (`curl`, `unzip`, `git`, `ca-certificates` — the sanctioned exception, matching prod's
    nginx `curl`). Any future tool must come from an official image (a service or a build
    stage), never an in-container apt-get.

---

## Runbook (Fresh Machine)

All compose commands run from the project root. They are Ctrl-safe to run on any OS
(macOS/Linux bash or Windows PowerShell — PowerShell uses `Copy-Item` below).

1. **Install prerequisites.** Docker with Compose v2, and Git.

2. **Download the project.**

   ```bash
   git clone <your-repo-url> planner
   cd planner
   ```

3. **Create your personal settings file from the dev template.**

   - **Mac / Linux:**

     ```bash
     cp laravel/.env.development laravel/.env
     ```

   - **Windows** (PowerShell):

     ```powershell
     Copy-Item laravel/.env.development laravel/.env
     ```

4. **Fill in 4 things in `laravel/.env`** (everything else is already right for dev):

   - `APP_KEY` — **required before the first `up`**: compose refuses to start the workspace
     without it (same guard as prod), so it must already exist in `laravel/.env` when you run
     step 5. Generate it on the host (the value must start with `base64:`):

      - **Mac / Linux:** `printf 'base64:%s\n' "$(openssl rand -base64 32)"`
      - **Windows** (PowerShell):
        `'base64:' + [System.Convert]::ToBase64String([System.Security.Cryptography.RandomNumberGenerator]::GetBytes(32))`

   - `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD` — any passwords you like.
     **Do this before the first `up`**: MySQL bakes the DB credentials when its data volume
     is created (see Troubleshooting).

5. **Build and start the dev stack.** First time is slow (pulls `php:8.4-cli`, `node:24`,
   `composer:2.10`, `mysql:8.4`, `redis:8.10`, installs extensions). Nothing runs
   automatically — the workspace just sleeps until you `exec` in:

   ```bash
   ./planner-dev up        # or the raw form:
   docker compose -f laravel/compose.development.yml up -d --build
   ```

6. **Open a terminal inside the workspace.** Run this once now and every future session:

   ```bash
   docker compose -f laravel/compose.development.yml exec workspace sh
   ```

   Install the project's dependencies once (they live on the bind mount, so they persist
   and only need re-installing when the lockfiles change):

   ```sh
   composer install
   npm install
   ```

7. **Start the servers.** In the workspace terminal(s):

   ```sh
   php artisan serve --host=0.0.0.0 --port=8000    # the app → http://localhost:8000
   npm run dev                                      # Vite + hot reload → :5173
   php artisan queue:listen --tries=1              # optional: dev queue worker
   ```

   (`--host=0.0.0.0` is required — `127.0.0.1` wouldn't be reachable from the host.)

   Easier: `composer run dev` runs all three (server + queue worker + Vite) at once.

8. **First use of the database.**

   ```sh
   php artisan migrate            # after mysql + redis are healthy (workspace waits for both)
   php artisan db:seed            # optional dev account, see Seeders
   ```

   Guest pages need no account, but to exercise the auth/task features register from the
   UI or use the seeder.

9. **Check it works.** Browser → <http://localhost:8000>; also
   <http://localhost:8000/up> should show `200`.

That's the whole loop. From now on: `up -d` (or `start`), `exec workspace sh`, code.

---

## Day-to-Day Operations

All commands from the project root. They all keep your dev data safe.

| Command | What it does |
|---|---|
| `docker compose -f laravel/compose.development.yml start` | bring the stack back up where it was |
| `docker compose -f laravel/compose.development.yml stop` | pause dev (frees resources) |
| `docker compose -f laravel/compose.development.yml up -d` | plain start with the current image |
| `docker compose -f laravel/compose.development.yml up -d --build` | rebuild the toolchain image (after a Dockerfile change), then start |
| `docker compose -f laravel/compose.development.yml down` | full stop, keeps the MySQL volume |
| `docker compose -f laravel/compose.development.yml exec workspace sh` | your daily entry point |
| `docker compose -f laravel/compose.development.yml logs -f workspace` | tail workspace logs (use `mysql` / `redis` for the others) |

Only use `down` when you want a totally clean slate; it never deletes data. **Never run
`docker compose -f laravel/compose.development.yml down -v`** — the `-v` wipes the MySQL dev volume.

Inside the workspace daily loop:

```sh
php artisan serve --host=0.0.0.0 --port=8000
npm run dev
composer test        # Pest, SQLite :memory: — no DB needed
php artisan migrate  # after any DB change
php artisan tinker   # scratch work
```

When to rebuild (**only** when the toolchain changes — never for app code):

- You edited `docker/development/php-cli/Dockerfile` (new tool/extension).

  ```bash
  docker compose -f laravel/compose.development.yml up -d --build
  ```

  The app code needs no rebuild — the bind mount serves it live.

---

## Verification & Troubleshooting

### Checking the stack

```bash
docker compose -f laravel/compose.development.yml ps     # workspace + mysql + redis: Up / running / healthy
docker compose -f laravel/compose.development.yml exec workspace sh
node --version && npm --version && composer --version && php -v
php -m | grep redis                        # phpredis extension loaded
curl -s http://localhost:8000/up           # expect 200 (artisan serve running)
```

### Troubleshooting table

| Symptom | Cause / fix |
|---|---|
| `npm: command not found` | image predates the node stage — run `up -d --build` |
| npm sharp/npm `EACCES` on `/.npm` | `HOME` must be `/home/workspace` (already in image); verify with `echo $HOME` |
| `up` aborts: `Set APP_KEY in .env` | compose enforces `APP_KEY` on the workspace (like prod) — fill a valid `base64:` key in `.env`, then `up` again |
| fresh clone: `artisan`/`npm` say no such file | deps not installed yet — nothing auto-installs; run `composer install` + `npm install` in the workspace once |
| `Composer detected issues in your platform` etc. on install | image/extension changed — rebuild the toolchain: `up -d --build` |
| `port ... already in use` (8000/5173) | something else owns one of the ports — free it or edit `ports:` in `laravel/compose.development.yml` |
| mysql unhealthy; `Access denied for user 'planner'` | credentials baked at first volume init differ from current `.env` — dev data is disposable: `down`, `docker volume rm planner-development_database`, then `up -d --build` and re-`migrate` |
| redis unhealthy; `NOAUTH` / `WRONGPASS` from the app | `REDIS_PASSWORD` in `.env` differs from the compose value passed at Redis start — make them match and recreate Redis (`up -d --force-recreate redis`) |
| `The stream or file "..." could not be opened` storage | the DB didn't come up first run or `depends_on` health; check `docker compose ... ps` and `.env` `DB_HOST`/`DB_PORT` |
| `artisan serve` runs but browser times out | forgot `--host=0.0.0.0` — that's the whole fix |
| Vite not hot-reloading | `usePolling` is already on; ensure 5173 is free and you reach it via `localhost:5173` |
| `app@... does not exist` tinker | run `composer install`, then `php artisan package:discover`, and restart tinker |

---

## Future Notes, Desires, and Not-Done

- **`php artisan serve` without flags still binds `127.0.0.1`** (Laravel's default). The
  `dev` script and the runbook both pass `--host=0.0.0.0`; making bare `php artisan serve`
  auto-bind `0.0.0.0` would need overriding Laravel's built-in `serve` command — deferred
  as unnecessary.
- **Redis encryption is prod-only.** Dev keeps `SESSION_ENCRYPT=false` so dev tools can
  read sessions; if that ever becomes a friction point, flip it on in `laravel/.env.development`
  and both stacks behave identically.
- **MySQL / Redis on a host port** for a GUI client is possible but deliberately not done —
  no host exposure, matching prod.
- **Friendly `planner` wrapper** — shipped: `./planner` (prod) and `./planner-dev`
  (dev) map the everyday commands (`./planner up`, `./planner ps`,
  `./planner-dev shell`, ...) to the raw `docker compose -f laravel/compose.*.yml`
  invocations the docs show.
- **prod ↔ dev on the same host**: both stacks read the *same git-ignored `laravel/.env`*.
  Only one environment runs at a time — switching means re-copying the template
  (`laravel/.env.production` or `laravel/.env.development`) and re-filling the four secrets. The mysql
  data volumes are separate per stack, so nothing is ever cross-contaminated.
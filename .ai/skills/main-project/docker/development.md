# Docker Development Setup

> Snapshot documentation. Read this before touching anything under `docker/`,
> `compose.dev.yaml`, or the `.env.*` templates.

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
| Dev model | Clone → copy template → fill `.env` → `up -d --build` → `exec workspace sh` |
| Orchestration | Docker Compose v2 (`compose.dev.yaml`), project name `planner-dev` |
| App | Laravel 13.x + Livewire 4.x (see `composer.json`) |
| Workspace image | `php:8.4-cli` (Debian/glibc) built from `docker/common/php-cli/Dockerfile` |
| Composer image (build-only) | `composer:2.10` |
| Node image (build-only) | `node:24-bookworm-slim` (same major as prod's `node:24-alpine`) |
| MySQL image | `mysql:8.4` (same as prod) |
| Containers | `planner-dev-workspace`, `planner-dev-mysql` |
| Named volumes | `planner-dev_mysql-data` (DB only; workspace is stateless) |
| Network | single user network `dev-network` → prefixed `planner-dev_dev-network` |
| Host ports | `8000` (artisan serve), `5173` (Vite HMR) — MySQL has **no** host port |
| PHP extensions | `pdo_mysql`, `pdo_sqlite`, `intl`, `mbstring`, `zip`, `bcmath`, `gd` |
| Redis | not present (app drivers are MySQL/database-backed in dev) |
| Tests | Pest on SQLite `:memory:` (via `phpunit.xml`) — needs `pdo_sqlite` |
| Restart policy | `unless-stopped` on all services |

Runtime state at the time of writing: the dev stack was running and the rebuilt
workspace image (node/composer/php) verified working by the user.

---

## Target Dev Model

- **One container = one dev terminal with everything inside**: PHP CLI + Composer + Node
  (npm/npx). Run Laravel with `artisan serve`, Vite with `npm run dev`, migrations, tests
  and Tinker — all from `<project>/docker`-provided tools.
- **No nginx, no php-fpm, no Redis in dev.** Production-service complexity is deferred.
- The project folder is **bind-mounted** `.` → `/var/www`, so every file you edit shows up
  in the container live (and vice versa). No image rebuild for app-code changes.
- The container runs as **your host UID/GID** (default `1000:1000`), so files created in
  the container are owned by your host user — no permission fights on the mount.
- MySQL is isolated inside the dev network (container name `mysql`), like prod. Dev DB
  data lives in its own volume and survives restarts.
- The repo is the deliverable: a fresh machine needs a clone, the filled-in `.env`,
  `composer install` + `npm install`, and the two dev servers.

---

## Architecture

```
        Host
  ┌──────────────┬───────────────────────────────┐
  │  :8000  ◄────┘    :5173                      │
  ▼                                              │
 planner-dev-workspace ◄── bind mount ./:/var/www │
 ┌─────────────────────┐                         │
 │ php:8.4-cli          │  artisan serve 0.0.0.0:8000
 │ + composer           │  Vite dev   0.0.0.0:5173
 │ + node / npm / npx   │  ──► mysql  (container DNS, :3306)
 └──────────┬───────────┘
            │ TCP:3306 (dev-network only — no host port)
            ▼
 planner-dev-mysql (mysql:8.4, data in planner-dev_mysql-data)
```

Key facts:

- Containers reach each other by **container name as DNS** inside the dev network:
  `mysql` is the DB host (matches `DB_HOST=mysql` in `.env.development`).
- **Only the workspace publishes host ports** (8000, 5173). MySQL is never exposed to the
  host — no GUI access by default (see [Future Notes](#future-notes-desires-and-not-done)).
- The workspace is **stateless**: all project files come from the bind mount; the image
  itself only carries the toolchain (~php + composer + node). `vendor/` and `node_modules/`
  live on the host and are shared through the mount.
- `CMD ["sleep", "infinity"]` plus `tty: true` + `stdin_open: true` keep the workspace
  alive so you can `exec` into it at any time. There is **no entrypoint** — you drive
  everything manually (unlike the prod stack, which auto-setups on boot).

---

## Repository Layout (Docker Parts)

```
/                      build context root
├── compose.dev.yaml                   dev stack definition
├── .env.development                  committed dev template (source of truth)
├── .env                              real values, git-ignored, copied from a template
├── docker/
│   └── common/
│       └── php-cli/Dockerfile        dev toolchain image (shared pattern dir)
├── vite.config.js                    dev server host / HMR / polling (see below)
├── composer.json                     `dev` / `test` / `setup` scripts
├── phpunit.xml                       test DB = sqlite `:memory:`
└── .ai/skills/main-project/docker/   production.md (prod) + this file
```

---

## File-by-File

### `compose.dev.yaml`

Single-file Compose v2 spec, project name `planner-dev`. The full file:

```yaml
name: planner-dev

services:
  workspace:
    container_name: planner-dev-workspace
    build:
      context: .
      dockerfile: ./docker/common/php-cli/Dockerfile
    restart: unless-stopped
    user: "${UID:-1000}:${GID:-1000}"
    tty: true
    stdin_open: true
    working_dir: /var/www
    env_file:
      - .env
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
      - dev-network
    depends_on:
      mysql:
        condition: service_healthy

  mysql:
    container_name: planner-dev-mysql
    image: mysql:8.4
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:-root}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - dev-network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-u", "root", "-p${MYSQL_ROOT_PASSWORD:-root}"]
      interval: 10s
      timeout: 5s
      retries: 5

networks:
  dev-network:

volumes:
  mysql-data:
```

Notes:

- `user: "${UID:-1000}:${GID:-1000}"` — the workspace runs as your uid/gid. On Linux,
  `UID`/`GID` are exported by most shells; if not, the fallback `1000:1000` is typical.
- `depends_on: mysql: condition: service_healthy` — the workspace starts only after the
  DB passes `mysqladmin ping`. DB credentials come from `.env` vars (defaults `root`,
  fallback `-root` is never used when `.env` is filled).
- No `entrypoint:` / `command:` — rely on `tty`/`sleep infinity`.

### `docker/common/php-cli/Dockerfile`

The entire dev toolchain in one **Debian-based** image. Full file:

```dockerfile
FROM node:24-bookworm-slim AS node

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

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && node --version \
    && npm --version \
    && composer --version

RUN mkdir -p /home/workspace && chown -R 1000:1000 /home/workspace

ENV HOME=/home/workspace

WORKDIR /var/www

CMD ["sleep", "infinity"]
```

Why it looks like this:

- **Debian (`php:8.4-cli`), not Alpine.** Node's official Linux binaries are musl-linked
  in the alpine builds but glibc-linked in the Debian builds; they can't run in a glibc
  PHP image. `node:24-bookworm-slim` is glibc and matches prod's Node 24. Going Alpine
  would have forced *both* PHP and Node to Alpine (a bigger parity deviation for no gain).
- **Composer is a PHP phar** — copying `/usr/bin/composer` from `composer:2.10` is fully
  portable and avoids a second install method. Same image the prod stack builds with.
- **Node + npm come from the `node:24-bookworm-slim` stage**, not a NodeSource script.
  The earlier draft ran the NodeSource install script (pulled in Python/pip cruft); the
  two-stage copy is smaller and version-pinned.
- **`npm`/`npx` are symlinks** into the copied `npm` package on `/usr/local/bin`.
- **`pdo_sqlite` + `libsqlite3-dev`** are required for Pest (`phpunit.xml` uses SQLite
  `:memory:`). Tests run inside this container; prod does *not* need pdo_sqlite.
- **No Redis extension** — dev sessions/cache/queue run on MySQL/database drivers
  (`SESSION_DRIVER=database`, `CACHE_STORE=database`). Add `pecl install redis` here the
  day dev actually talks to Redis (see [Future Notes](#future-notes-desires-and-not-done)).
- **`HOME=/home/workspace`** (created and owned by `1000:1000`): npm/composer/git write
  their caches under `$HOME`. Without it the container tried `/.npm` (root-owned, EACCES —
  the exact failure seen during setup). Reading/npm/git caches now live in `/home/workspace`.
- The version guards (`node --version`, `npm --version`, `composer --version`) run at
  build time so a broken copy fails the build instead of surprising you at runtime.

### `.env.development`

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

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_EXPIRE_ON_CLOSE=false

CACHE_STORE=database
```

Only four things are blank and must be filled in `.env`:
`APP_KEY`, `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`. Everything else has a config default or
is unused in dev.

- `DB_HOST=mysql` — inside the dev network, the DB is reachable by its container name.
- `APP_URL=http://localhost:8000` — matches the artisan-serve port.
- Dev friendly: `APP_ENV=local`, `APP_DEBUG=true`, `LOG_LEVEL=debug`.
- Removed from stock Laravel: BROADCAST/QUEUE/FILESYSTEM/MEMCACHED/REDIS/MAIL/AWS/VITE —
  each verified to have a sane config default or be unused in dev. Re-add a **REDIS**
  group only if/when dev uses Redis.

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

- `composer.json` scripts relevant in dev: `composer dev` (concurrent: `artisan serve` +
  `queue:listen` + `npm run dev`), `composer test` (calls `artisan test` = Pest).
- **Gotcha:** the `dev` script's `php artisan serve` runs with Vite connected but binds
  the default `127.0.0.1`, which is *not reachable* from the host through the 8000
  port mapping. The runbook below therefore uses the explicit
  `php artisan serve --host=0.0.0.0 --port=8000`. Fixing the script itself is deferred
  (see [Future Notes](#future-notes-desires-and-not-done)).
- `phpunit.xml` uses `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` — tests need no DB
  container and run anywhere with `pdo_sqlite`.

---

## Environment Variables: Host → Container

| Compose/dev file value | Source in `.env` | Notes |
|---|---|---|
| `workspace.env_file` | entire `.env` | all variables (APP_*, DB_*, SESSION_*, CACHE_*) injected into the container |
| `MYSQL_ROOT_PASSWORD` | `${MYSQL_ROOT_PASSWORD:-root}` | bake at first `up` → changing later needs the volume reset (see Troubleshooting) |
| `MYSQL_DATABASE` | `${DB_DATABASE}` | must equal `DB_DATABASE` from the workspace's `.env` |
| `MYSQL_USER` | `${DB_USERNAME}` | ditto |
| `MYSQL_PASSWORD` | `${DB_PASSWORD}` | ditto |
| workspace port `8000` | — (static) | artisan serve |
| workspace port `5173` | — (static) | Vite HMR |

Rule of thumb: keep `DB_HOST=mysql`, `DB_PORT=3306`, and the four `DB_*`/`MYSQL_*` values
in sync between the two services — Compose reads them from the *same* `.env`.

---

## Decisions Log

1. **One container with every tool** (`php-cli` + composer + node). Matches the stated
   goal "exec workspace sh → a terminal with everything"; avoids 3-4 tiny containers.
2. **Debian base, not Alpine.** Node alpine (musl) cannot run in a glibc PHP image.
   Staying Debian for PHP keeps the closest parity with prod (`php:8.4-fpm` is also
   Debian-based). Node 24 comes from the **glibc** `node:24-bookworm-slim` build.
3. **Node via image stage, not NodeSource.** Smaller, pinned, no Python/pip baggage that
   the first draft's install script pulled in.
4. **Two-stage copying (composer phar + node)** instead of multi-exec installers. If any
   copy breaks, the build-time version guards fail loudly.
5. **No nginx/php-fpm in dev.** `artisan serve` is enough for local work. The prod stack's
   nginx/fpm (and its "assets built twice" dance) stay production-only.
6. **No Redis in dev** (image or service). All dev drivers sit on MySQL/database
   (`CACHE_STORE=database`, `SESSION_DRIVER=database`). Redis is prod-only until the app
   actually uses it in a way that matters locally.
7. **MySQL 8.4 same as prod**, but with its own dev volume and **no host port** — the
   database is only reachable from the workspace (matches prod's posture).
8. **Bind mount `./:/var/www`** — code changes are live; the image is a toolchain, not an
   app artifact. Rebuild only when the *toolchain* changes.
9. **`user: ${UID:-1000}:${GID:-1000}` + `HOME=/home/workspace`** — cache dirs
   (`~/.npm`, `~/.composer`, `~/.git`) are writable and all container-created files belong
   to the host user. This fixed the real `EACCES /.npm` failure hit during setup.
10. **`tty`/`stdin_open` + `CMD sleep infinity`, no entrypoint.** Dev is interactive and
    manual; prod's auto-setup entrypoint has no place here.
11. **`pdo_sqlite` kept** so Pest (`sqlite :memory:`) works in the same container.
12. **`.env.development` = template with blank secrets**, same contract as the prod
    template: real values only ever live in the git-ignored `.env`.

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
     cp .env.development .env
     ```

   - **Windows** (PowerShell):

     ```powershell
     Copy-Item .env.development .env
     ```

4. **Fill in 3 things in `.env`** (everything else is already right for dev):

   - `APP_KEY` — generate one. Either on the host:

     - **Mac / Linux:** `openssl rand -base64 32`
     - **Windows** (PowerShell):
       `[System.Convert]::ToBase64String([System.Security.Cryptography.RandomNumberGenerator]::GetBytes(32))`

     …or, easiest, inside the container first time (see step 6), run
     `php artisan key:generate` — it writes straight back into the bind-mounted `.env`.

   - `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD` — any passwords you like. **Do this before the
     first `up`**: MySQL bakes them when its data volume is created (see Troubleshooting).

5. **Build and start the dev stack.** First time is slow (pulls `php:8.4-cli`,
   `node:24-bookworm-slim`, `composer:2.10`, `mysql:8.4`, installs extensions):

   ```bash
   docker compose -f compose.dev.yaml up -d --build
   ```

6. **Open a terminal inside the workspace.** Run this once now and every future session:

   ```bash
   docker compose -f compose.dev.yaml exec workspace sh
   ```

7. **First time only** (vendor/ and node_modules/ don't exist yet):

   ```sh
   composer install
   npm install
   ```

8. **Start the servers.** In the workspace terminal(s):

   ```sh
   php artisan serve --host=0.0.0.0 --port=8000    # the app → http://localhost:8000
   npm run dev                                      # Vite + hot reload → :5173
   php artisan queue:listen --tries=1              # optional: dev queue worker
   ```

   (`--host=0.0.0.0` is required — `127.0.0.1` wouldn't be reachable from the host.)

9. **First use of the database.**

   ```sh
   php artisan migrate            # after mysql is healthy (workspace waits for it)
   php artisan db:seed            # optional dev account, see Seeders
   ```

   Guest pages need no account, but to exercise the auth/task features register from the
   UI or use the seeder.

10. **Check it works.** Browser → <http://localhost:8000>; also
    <http://localhost:8000/up> should show `200`.

That's the whole loop. From now on: `up -d` (or `start`), `exec workspace sh`, code.

---

## Day-to-Day Operations

All commands from the project root. They all keep your dev data safe.

| Command | What it does |
|---|---|
| `docker compose -f compose.dev.yaml start` | bring the stack back up where it was |
| `docker compose -f compose.dev.yaml stop` | pause dev (frees resources) |
| `docker compose -f compose.dev.yaml up -d` | plain start with the current image |
| `docker compose -f compose.dev.yaml up -d --build` | rebuild the toolchain image (after a Dockerfile change), then start |
| `docker compose -f compose.dev.yaml down` | full stop, keeps the MySQL volume |
| `docker compose -f compose.dev.yaml exec workspace sh` | your daily entry point |
| `docker compose -f compose.dev.yaml logs -f workspace` | tail workspace logs (use `mysql` for the DB) |

Only use `down` when you want a totally clean slate; it never deletes data. **Never run
`docker compose -f compose.dev.yaml down -v`** — the `-v` wipes the MySQL dev volume.

Inside the workspace daily loop:

```sh
php artisan serve --host=0.0.0.0 --port=8000
npm run dev
composer test        # Pest, SQLite :memory: — no DB needed
php artisan migrate  # after any DB change
php artisan tinker   # scratch work
```

When to rebuild (**only** when the toolchain changes — never for app code):

- You edited `docker/common/php-cli/Dockerfile` (new tool/extension).

  ```bash
  docker compose -f compose.dev.yaml up -d --build
  ```

  The app code needs no rebuild — the bind mount serves it live.

---

## Verification & Troubleshooting

### Checking the stack

```bash
docker compose -f compose.dev.yaml ps     # workspace + mysql: Up / running / healthy
docker compose -f compose.dev.yaml exec workspace sh
node --version && npm --version && composer --version && php -v
curl -s http://localhost:8000/up           # expect 200 (artisan serve running)
```

### Troubleshooting table

| Symptom | Cause / fix |
|---|---|
| `npm: command not found` | image predates the node stage — run `up -d --build` |
| npm sharp/npm `EACCES` on `/.npm` | `HOME` must be `/home/workspace` (already in image); verify with `echo $HOME` |
| workspace exits immediately | almost always a blank `APP_KEY` — run `php artisan key:generate` in it, then `restart workspace` |
| `port ... already in use` (8000/5173) | something else owns one of the ports — free it or edit `ports:` in `compose.dev.yaml` |
| mysql unhealthy; `Access denied for user 'planner'` | credentials baked at first volume init differ from current `.env` — dev data is disposable: `down`, `docker volume rm planner-dev_mysql-data`, then `up -d --build` and re-`migrate` |
| `The stream or file "..." could not be opened` storage | the DB didn't come up first run or `depends_on` health; check `docker compose ... ps` and `.env` `DB_HOST`/`DB_PORT` |
| `artisan serve` runs but browser times out | forgot `--host=0.0.0.0` — that's the whole fix |
| Vite not hot-reloading | `usePolling` is already on; ensure 5173 is free and you reach it via `localhost:5173` |
| `app@... does not exist` tinker | run `composer install` once, then `php artisan package:discover` |

---

## Future Notes, Desires, and Not-Done

- **`composer dev` alias** currently runs `php artisan serve` without `--host=0.0.0.0`,
  so it can't serve to the host. Either patch that script or keep using the explicit
  command from the runbook (documented approach).
- **Redis in dev**: add a `redis` service, a `REDIS_*` env group in `.env.development`,
  and `pecl install redis` in the Dockerfile the day the app adopts Redis-backed queueing
  (prod grew a persistence note for exactly this).
- **MySQL on a host port** for a GUI client is possible but deliberately not done — no
  host exposure, matching prod.
- **Friendly `planner` wrapper** (`planner up`, `planner down`, `planner dev`) covering
  both stacks is deferred; the docs use explicit `docker compose -f compose.*.yaml`.
- **prod ↔ dev on the same host**: both stacks read the *same git-ignored `.env`*.
  Only one environment runs at a time — switching means re-copying the template
  (`.env.production` or `.env.development`) and re-filling the few values. The mysql
  data volumes are separate per stack, so nothing is ever cross-contaminated.
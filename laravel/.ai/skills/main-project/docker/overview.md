# Docker Overview (shared conventions)

Applies to both the development and production stacks. Read this first — `development.md`
and `production.md` reference it instead of repeating it.

> Reference documentation. Point of truth is the actual files under `docker/`,
> `laravel/compose.*.yml`, and the `.env.*` templates; this file describes them.

## Stack Shape

| Service | Image / source | Dev | Prod |
|---|---|---|---|
| App runtime | dev `php:8.4-cli` (workspace) / prod `php:8.4-fpm` | `planner-development-workspace` | `planner-php-fpm` |
| Web | none (artisan serve) | — | `planner-nginx` (`nginx:1.30`) |
| MySQL | `mysql:8.4` (official) | no host port, own volume | `planner-mysql` |
| Redis | `redis:8.10` (official) | no host port | `planner-redis` |
| Build tools | `composer:2.10` + `node:24` (stages, never installed in-image) | both stacks | both stacks |

Service names are the DNS hostnames inside the shared user network (`DB_HOST=mysql`,
`REDIS_HOST=redis`). Compose project names are hard-coded in the compose files
(`planner-development` / `planner`), so volume prefixes (`planner-development_`,
`planner_`) do not depend on the checkout directory.

## Credential handling (`.env` contract)

- Real values live only in the git-ignored `laravel/.env`, copied from a committed template
  (`laravel/.env.development` or `laravel/.env.production`). The templates commit real
  non-secret values and leave only the four secrets blank: `APP_KEY`, `DB_PASSWORD`,
  `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD`.
- **`APP_KEY` must be `base64:` + a 32-byte key** (AES-256-CBC). Generate with
  `printf 'base64:%s\n' "$(openssl rand -base64 32)"`. Dev enforces it via compose
  interpolation guard; prod additionally validates the exact format in `entrypoint.sh`.
- All four secrets are enforced at the compose level with `${VAR:?msg}` — Compose aborts
  immediately naming the missing one. Never use `:-root` / `:-` style fallbacks for secrets.
- **Never set `MYSQL_PWD`.** The mysql image's first-boot bootstrap connects to a temporary
  passwordless server; a stray `MYSQL_PWD` injects a password into every such connection
  and the entrypoint's own `ALTER USER`/`CREATE USER` fail, leaving a half-initialized data
  dir. Credentials reach the client only via Compose interpolation (`-p${MYSQL_ROOT_PASSWORD}`)
  or `REDISCLI_AUTH` env.
- Changing `.env` requires recreating affected containers (a running container freezes its
  env at start). Compose re-reads `.env` on every command, so `up -d` suffices.

## Healthcheck contract

- All healthchecks are **exec-form** `CMD` (no shell): mysql does a real `mysql ... -e
  'SELECT 1'` round-trip (not `mysqladmin ping`, which passes even on failed auth), redis
  uses `redis-cli ping` (authed via `REDISCLI_AUTH`), php-fpm opens TCP 9000 via
  `php -r fsockopen`, nginx hits `curl -fs http://127.0.0.1/up`.
- Boot order via `depends_on: condition: service_healthy`: mysql + redis → app → nginx
  (prod). Services do not start until dependencies pass health checks; mysql has
  `start_period: 30s` for slow first-boot init.
- Every service: `restart: unless-stopped` and `logging: json-file, max-size 10m, max-file 3`.

## `planner` / `planner-dev` wrappers

Repo-root scripts map everyday commands onto the raw compose invocations:

```bash
./planner up           # = docker compose -f laravel/compose.production.yml up -d --build
./planner ps
./planner-dev shell    # = ... exec workspace sh (dev)
./planner-dev up
```

Both stacks read the same `laravel/.env`. Only one environment runs at a time — switching
means re-copying the other template and re-filling the secrets. Data volumes are separate
per stack, so nothing cross-contaminates.

## Production seeding decision

`docker/production/php-fpm/entrypoint.sh` runs `migrate --seed --force` on every boot, so
production **does seed**. `database/seeders/UserSeeder.php` is currently an empty no-op
placeholder, so seeding has no effect today; if that seeder ever creates real users, revisit
this line before deploying (a known-credential dev seed must not reach production).

## Shared context worth knowing

- Assets are built **twice** on the prod path — once in each image — with the same pins and
  lockfiles, so nginx can serve the exact files php-fpm references via `@vite`.
- Prod has **no bind mounts**; the repo is the immutable artifact (build context is the repo
  root, filtered by `.dockerignore`). Dev bind-mounts the repo into the workspace.
- `npm ci --no-audit --no-fund` everywhere for reproducible builds from lockfiles.
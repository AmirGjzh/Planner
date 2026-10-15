# Docker Shared Conventions

Applies to both the development and production stacks. Read this first — `development.md`
and `production.md` reference it instead of repeating it.

> Reference documentation. Point of truth is the actual files under `docker/`,
> `laravel/compose.*.yml`, and the `.env.*` templates; this file describes them.

## Stack Shape

| Service | Image / source | Dev | Prod |
|---|---|---|---|
| App runtime | dev `php:8.4-cli` (workspace) / prod `php:8.4-fpm` | `planner-workspace` | `planner-php-fpm` |
| Queue worker | consumes the redis queue | `queue:listen` inside the workspace (`composer run dev`) | `planner-queue` (same image as php-fpm, `queue:work`, `SKIP_MIGRATE=1` so it doesn't re-run boot migrations) |
| Web | none (artisan serve) | — | `planner-nginx` (`nginx:1.30-alpine`) |
| MariaDB | `mariadb:12.3` (official) | no host port, own volume | `planner-mariadb` |
| Redis | `redis:8.10-alpine` (official, `--appendonly yes` + volume) | no host port | `planner-redis` |
| Mail | `axllent/mailpit:v1.27` (SMTP capture + web UI) | `planner-development-mailpit` (UI :8025) | `planner-mailpit` (UI :8025) |
| Build tools | `node:24-slim` (dev: donor for the node/npm binaries copied into the workspace; prod: frontend build stage) + Composer phar 2.10.3 + sha256 pinned identically in both builders | both stacks | both stacks |

Service names are the DNS hostnames inside the shared user network (`DB_HOST=mariadb`,
`REDIS_HOST=redis`, `MAIL_HOST=mailpit`). Compose project names are hard-coded in the compose files
(`planner-development` / `planner`), so volume prefixes (`planner-development_`,
`planner_`) do not depend on the checkout directory.

## Credential handling (`.env` contract)

- Real values live only in the git-ignored `laravel/.env`, copied from a committed template
  (`laravel/.env.development` or `laravel/.env.production`). The templates commit real
  non-secret values and leave only the secrets blank: `APP_KEY`, `DB_PASSWORD`,
  `MARIADB_ROOT_PASSWORD`, `REDIS_PASSWORD`.
- **`APP_KEY` must be `base64:` + a 32-byte key** (AES-256-CBC). Generate with
  `printf 'base64:%s\n' "$(openssl rand -base64 32)"`. Dev enforces it via compose
  interpolation guard; prod additionally validates the exact format in `entrypoint.sh`.
- All secrets are enforced at the compose level with `${VAR:?msg}` — Compose aborts
  immediately naming the missing one. Never use `:-root` / `:-` style fallbacks for secrets.
- **Never set `MARIADB_PWD`.** The mariadb image's first-boot bootstrap connects to a temporary
  passwordless server; a stray `MARIADB_PWD` injects a password into every such connection
  and the entrypoint's own `ALTER USER`/`CREATE USER` fail, leaving a half-initialized data
  dir. Credentials reach the client only via Compose interpolation (`-p${MARIADB_ROOT_PASSWORD}`)
  or `REDISCLI_AUTH` env.
- Changing `.env` requires recreating affected containers (a running container freezes its
  env at start). Compose re-reads `.env` on every command, so `up -d` suffices.

## Healthcheck contract

- All healthchecks are **exec-form** `CMD` (no shell): mariadb does a real `mariadb ... -e
  'SELECT 1'` round-trip (not `mariadb-admin ping`, which passes even on failed auth), redis
  uses `redis-cli ping` (authed via `REDISCLI_AUTH`), php-fpm opens TCP 9000 via
  `php -r fsockopen`, nginx hits `curl -fs http://127.0.0.1/up`. Mailpit is the one
  exception in bookkeeping, not in form: its image ships a built-in exec-form
  `HEALTHCHECK CMD ["/mailpit", "readyz"]`, so neither compose file declares one.
- Boot order via `depends_on: condition: service_healthy`: mariadb + redis → app → nginx +
  queue (prod); php-fpm and queue additionally wait for mailpit `service_started` (SMTP
  reachable before any send). Services do not start until dependencies pass health checks;
  mariadb has
  `start_period: 30s` for slow first-boot init. The queue worker has no port and no
  healthcheck (mailpit precedent) — it is ordered after healthy php-fpm so boot-time
  `migrate --seed` never races; its entrypoint then skips the (already finished) migrate
  run via `SKIP_MIGRATE=1`.
- Every service: `restart: unless-stopped` and `logging: json-file, max-size 10m, max-file 3`.

## `planner` / `planner-dev` wrappers

Repo-root scripts map everyday commands onto the raw compose invocations:

```bash
./planner up           # = docker compose -f laravel/compose.production.yml up -d
./planner ps
./planner logs [svc]   # = ... logs -f (all services, or one)
./planner-dev shell    # = ... exec workspace sh (dev)
./planner-dev up
```

Both stacks read the same `laravel/.env`. Only one environment runs at a time — switching
means re-copying the other template and re-filling the secrets. Data volumes are separate
per stack, so nothing cross-contaminates.

## Production seeding decision

`docker/production/entrypoint.sh` runs `migrate --seed --force` on every php-fpm boot, so
production **does seed**. The queue service sets `SKIP_MIGRATE=1`, so its entrypoint skips
both migrate and seed (php-fpm — which it waits for — already did them).
`database/seeders/UserSeeder.php` is currently an empty no-op
placeholder, so seeding has no effect today; if that seeder ever creates real users, revisit
this line before deploying (a known-credential dev seed must not reach production).

## Shared context worth knowing

- Prod builds assets **once** in a shared `frontend` build stage; both final images copy
  from that single build, so nginx serves the exact files php-fpm references via `@vite`.
- Prod has **no bind mounts**; the repo is the immutable artifact (build context is the repo
  root, filtered by `.dockerignore`). Dev bind-mounts the repo into the workspace.
- `npm ci --no-audit --no-fund` everywhere for reproducible builds from lockfiles.

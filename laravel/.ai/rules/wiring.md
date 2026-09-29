---
paths:
  - bootstrap/app.php
  - config/database.php
  - config/queue.php
---

# App Wiring

Rules for `bootstrap/app.php`, `config/database.php`, and `config/queue.php` (production-critical plumbing).

## bootstrap/app.php: proxies and redirects are deliberate

- `trustProxies(at: '*')` — nginx is the only proxy in front and sets `X-Forwarded-*` for every request; a proxy IP list was rejected (Docker bridge IP varies).
- `redirectGuestsTo('/login')`, `redirectUsersTo('/dashboard')`.
- `ExpireSessionsOnPasswordChange` is appended to the `web` group — session freshness against `password_changed_at` must cover every authenticated surface (pages *and* the Livewire update route), not selected routes; don't move it to route-level middleware.
- Keep the proxy/redirect setup; don't narrow the trusted proxy set without re-checking the compose topology.

## config/database.php: the session connection stays isolated

- Three redis connections: `default` (DB 0), `cache` (DB 1), `session` (DB 2) — key-space isolation on the single Redis instance.
- `SESSION_DRIVER` uses the dedicated `session` connection; `Route::block()` locks on the cache-lock redis connection (`REDIS_CACHE_LOCK_CONNECTION`), never the session connection.
- Never collapse sessions onto `default`/`cache` — flushing cache would nuke sessions and lock/block behavior would drift.

## config/queue.php: redis-backed, sync under test

- `QUEUE_CONNECTION=redis` in both `.env.*` templates — jobs live on the `default` redis connection (DB 0, isolated from cache/session), and the prod `queue` worker service (`compose.production.yml`) is the only consumer.
- Failed jobs land in the `failed_jobs` table (`database-uuids` driver); phpunit forces `QUEUE_CONNECTION=sync`, so tests execute jobs inline and never touch redis.

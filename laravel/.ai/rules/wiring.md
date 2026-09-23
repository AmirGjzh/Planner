---
paths:
  - bootstrap/app.php
  - config/database.php
---

# App Wiring

Rules for `bootstrap/app.php` and `config/database.php` (production-critical plumbing).

## bootstrap/app.php: proxies and redirects are deliberate

- `trustProxies(at: '*')` — nginx is the only proxy in front and sets `X-Forwarded-*` for every request; a proxy IP list was rejected (Docker bridge IP varies).
- `redirectGuestsTo('/login')`, `redirectUsersTo('/dashboard')`.
- Keep both; don't narrow the trusted proxy set without re-checking the compose topology.

## config/database.php: the session connection stays isolated

- Three redis connections: `default` (DB 0), `cache` (DB 1), `session` (DB 2) — key-space isolation on the single Redis instance.
- `SESSION_DRIVER` uses the dedicated `session` connection; `Route::block()` locks on the cache-lock redis connection (`REDIS_CACHE_LOCK_CONNECTION`), never the session connection.
- Never collapse sessions onto `default`/`cache` — flushing cache would nuke sessions and lock/block behavior would drift.

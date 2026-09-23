---
paths:
  - 'routes/**'
---

# Routes

Rules for `routes/web.php` and the Livewire update endpoint.

## Every route and the Livewire update endpoint carry `->block(10, 10)`

- All web routes and the Livewire update endpoint use `->block(10, 10)` (10s timeout, 10s lock) on the cache-lock redis connection — overlapping duplicate requests for the same session wait instead of double-writing (sessions live in Redis, where stale concurrent writes are a real risk).
- The Livewire update route is redeclared with the same `block` because Livewire registers its own copy; the redeclaration must win.
- New routes must include `block()` — see `AppServiceProvider::boot()` for the update-route wiring.

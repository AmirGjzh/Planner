---
paths:
  - 'app/Actions/**'
  - 'resources/views/pages/**'
---

# Actions

Rules for the domain-layer Action classes in `app/Actions/**`. The "Indexed date filters" rule also applies to the query chains that live in Livewire pages.

## Rate-limit keys: `kebab-prefix:identifier`

All `RateLimiter` keys follow one of two shapes, built with `Str::transliterate()`:

| Scope | Shape | Example |
| --- | --- | --- |
| Guest | `prefix:{ip}` | `register:{ip}`, `login:{ip}` |
| Authenticated | `prefix:{user_id}|{ip}` | `create-task:{user_id}|{ip}`, `edit-plan:{user_id}|{ip}`, `toggle-task:{user_id}|{ip}` |

- Clear the bucket on success (`RateLimiter::clear(...)`) — unless the bucket caps volume or leaks existence: the auth exceptions that must never clear are documented in `.ai/rules/auth.md`.
- Log `available_in` for rate-limited outcomes.
- Reuse an existing shape — never add a second ad-hoc limiter for a covered case.

## Authorize with `$user->can()` + `abort_unless`

Authorize inside the Action with `abort_unless($user->can('ability', $modelOrClass), 403)` — never `$this->authorize()`, `@can`, or auth middleware.

- Self-only actions check `$user->is(auth()->user())`.
- Policies live in `app/Policies` and auto-discover (no `Gate::define`).

## Keep query logic as inline Eloquent builder chains

All query/filter logic stays as Eloquent builder chains inside the Action.

- No repositories, dedicated query objects, or model scopes.
- Return domain result enums (`XxxResult`); inject `LoggerInterface` for rate-limit/warning/info logging.

## Actions are final, plain PHP — no DTOs or events

- Every Action is a `final class` with a single public entry method (`execute(...)`, or domain-named like `ReportsAction::summary()`/`chart()`).
- Params: primitives and—when IP rate-limiting applies—a `Request`. Only injected dependency: `LoggerInterface` (constructor property promotion).
- Returns plain values or `XxxResult` enums.
- The project has no `app/Events`, listeners, or DTO/data classes — route results through the owning page Livewire component, never through events. Queues are fire-and-forget side effects only: currently the reset and verification notifications (`ShouldQueue`), dispatched inside the owning Action under its `try/catch`; never dispatch a job to produce or deliver a page result.

## Indexed date filters

Compare DATE columns with plain `where()`, never `whereDate()` — the grammar compiles it to `date(\`col\`) op ?`, which wraps the column in a function and makes composite indexes unusable. All task/plan date columns are already type DATE, so `where('task_date', '>=', $start)` is equivalent. Pass date-only values: `now()->toDateString()` / `$today->toDateString()` — `whereDate` used to truncate the Carbon value for you, and a raw `now()` carries a time component that would exclude same-day rows.

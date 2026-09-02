---
paths:
  - 'app/Actions/**'
---

# Actions

## Rate-limit key shape: prefix:ID (guest) / prefix:userID|IP (auth)
All RateLimiter keys follow one of two consistent shapes. Guest-scoped actions key by a single identifier: `register:{ip}`, `login:{ip}`. Authenticated actions key by user + IP: `create-task:{user_id}|{ip}`, `edit-plan:{user_id}|{ip}`, `toggle-task:{user_id}|{ip}`, etc. Key is always built via `Str::transliterate('prefix:'. [...])` and cleared on success. Keep new limit keys to `kebab-prefix:identifier` — never add a second ad-hoc limiter when one key shape already covers the case. Logs include `available_in` for rate-limited outcomes.

## Authorize with $user->can() + abort_unless
Authorize inside Actions using `abort_unless($user->can('ability', $modelOrClass), 403)`; never $this->authorize(), @can, or auth middleware. For self-only actions use `$user->is(auth()->user())`. Policy classes in app/Policies are auto-discovered (no Gate::define).

## Inline Eloquent builder chains in Actions
Keep all query/filter logic as Eloquent builder chains inside final Action classes (app/Actions/**); no Repositories, dedicated Query objects, or Model scopes. Return domain result enums (XxxResult) and inject LoggerInterface for rate-limit/warning/info logging.

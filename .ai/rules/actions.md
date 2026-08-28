---
paths:
  - 'app/Actions/**'
---

# Actions

## Rate-limit key shape: prefix:ID (guest) / prefix:userID|IP (auth)
All RateLimiter keys follow one of two consistent shapes. Guest-scoped actions key by a single identifier: `register:{ip}`, `login:{ip}`. Authenticated actions key by user + IP: `create-task:{user_id}|{ip}`, `edit-plan:{user_id}|{ip}`, `toggle-task:{user_id}|{ip}`, etc. Key is always built via `Str::transliterate('prefix:'. [...])` and cleared on success. Keep new limit keys to `kebab-prefix:identifier` — never add a second ad-hoc limiter when one key shape already covers the case. Logs include `available_in` for rate-limited outcomes.

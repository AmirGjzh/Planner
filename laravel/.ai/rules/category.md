---
paths:
  - 'app/Actions/Category/**'
---

# Category

Audit decisions for category CRUD (UC-06).

## Normalize names only inside Create/Edit Actions

Name normalization (`trim` + edge half-space strip + `Str::ucfirst(Str::lower())`) lives only in `CreateCategoryAction`/`EditCategoryAction`. The page canonicalizes the field before validation (the register-username pattern — whitespace-only input must hit the `required` message) and passes the canonical, otherwise raw value; never lowercase/ucfirst in the Livewire component. `trim()` is byte-wise and blind to ZWNJ, so both layers strip edge `نیم‌فاصله` with profile's unicode regex (`/^[\s\x{200C}]+|[\s\x{200C}]+$/u`) — otherwise a visually identical name dodges the unique index, and a half-space-only name reads as invisible. Mid-name ZWNJ↔space unification is deliberately not done here (a user decision, tracked in `Refactor.md`).

## Search is case-insensitive and collation-independent

Use `whereRaw('LOWER(name) LIKE ?', [Str::lower('%'.$term.'%')])` — a plain `LIKE` won't match reliably across collations. The term is `trim()`med once in the computed: storage canonicalizes names, so search must trim too (covers both keystrokes and `?search=` URL entries).

## Create/Edit buckets count duplicate attempts; Delete stays unthrottled

`create-category:` and `edit-category:` are 5/min failure counters keyed `prefix:{user_id}|{ip}`: the bucket is hit only on the duplicate-name outcome — the sole failure these actions have — and cleared on success, the shape every create/edit mutation already shares (task and plan). Delete carries no limiter: the ownership policy plus the server-side `count()` guard make it low-risk (mirrors `.ai/rules/plan.md`). Rate-limited outcomes log `available_in`.

## Create/Edit duplicate race is intentionally unprotected

The DB composite unique index (`user_id, name`) plus an `exists()` pre-check plus rate limiting are enough; no `QueryException` handling around inserts (the register/profile pattern is not mirrored here).

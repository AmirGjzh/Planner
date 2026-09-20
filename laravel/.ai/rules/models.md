---
paths:
  - 'app/Models/**'
---

# Models

How Eloquent models are written.

## Fillable via `#[Fillable]` attribute; casts via `casts()` method

- Whitelist attributes with the `#[Fillable([...])]` attribute (and `#[Hidden]` on User) — not `$fillable`/`$guarded` arrays.
- Use the `casts()` method with built-in strings and enum `::class` casts.
- No custom `CastsAttributes` or accessor/mutator `Attribute`s needed.
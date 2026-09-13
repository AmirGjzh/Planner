---
paths:
  - 'app/Models/**'
---

# Models

## Model fillable via #[Fillable] attribute; casts() method
Whitelist attributes with the `#[Fillable([...])]` attribute (and `#[Hidden]` on User) instead of `$fillable`/`$guarded` arrays. Use the `casts()` method with built-in strings and enum `::class` casts; no custom CastsAttributes or accessor/mutator Attributes needed.

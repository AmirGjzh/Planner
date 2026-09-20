---
paths:
  - 'app/Actions/Category/**'
---

# Category

Audit decisions for category CRUD (UC-06).

## Normalize names only inside Create/Edit Actions

Name normalization (`Str::ucfirst(Str::lower())`) lives only in `CreateCategoryAction`/`EditCategoryAction`. Pass the raw validated value from the page; never normalize in the Livewire component.

## Search is case-insensitive and collation-independent

Use `whereRaw('LOWER(name) LIKE ?', [Str::lower('%'.$term.'%')])` — a plain `LIKE` won't match reliably across collations.

## Create/Edit duplicate race is intentionally unprotected

The DB composite unique index (`user_id, name`) plus an `exists()` pre-check plus rate limiting are enough; no `QueryException` handling around inserts (the register/profile pattern is not mirrored here).
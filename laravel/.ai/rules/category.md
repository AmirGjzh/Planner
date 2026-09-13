---
paths:
  - 'app/Actions/Category/**'
---

# Category

## Category name normalization + case-insensitive search
UC-06 audit decisions: (1) Category name normalization (Str::ucfirst(Str::lower())) lives ONLY in CreateCategoryAction/EditCategoryAction — never normalize in the Livewire page; pass the raw validated value to the action. (2) Category search must be case-insensitive and collation-independent: use whereRaw('LOWER(name) LIKE ?', [Str::lower('%'.$term.'%')]). (3) Create/Edit duplicate-name race (TOCTOU) is deliberately left unprotected by QueryException handling — the DB composite unique (user_id,name) index + exists() pre-check + rate limit are enough; register/profile pattern is not mirrored here.

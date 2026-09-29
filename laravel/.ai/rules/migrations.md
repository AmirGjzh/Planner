---
paths:
  - 'database/migrations/**'
---

# Migrations

Column and index conventions for migrations.

## Fixed value sets are `string` columns + PHP enums — never DB `enum()`

- DB column: `->string('priority')->default('medium')` — the PHP enum is the single source of truth (`TaskPriority`, `UserGender`), enforced by the model cast and `Rule::enum(Xxx::class)` validation.
- Never use `->enum()`: adding a value then requires an `ALTER`, MySQL and SQLite would store different types (real ENUM vs `varchar check`), and an unexpected value errors at the DB instead of failing in app code.
- Never duplicate an enum as a hard-coded `'in:low,medium,high'` rule — validate with `Rule::enum(...)`. Config-driven value lists (themes) keep `Rule::in(config('themes.name'))`.
- Keep a `default()` on the column matching the model/DB default.

## Unsigned integers for every ≥ 0 column

`estimated_minutes` → `unsignedInteger`, `day_before_alarm` → `unsignedSmallInteger` (bounded by validation). Booleans stay `boolean()`.

## Index led by `user_id` — every query is user-scoped first

- Hot-path filters/aggregations get composites with `user_id` as the leftmost column: `['user_id', 'task_date']` (workload, reports, date ranges) and `['user_id', 'done']` (attention list, status filters). Single-column `task_date`/`done` global indexes are never the right shape here.
- Never add a single-column index on a column already led by a unique composite (e.g. `index('user_id')` next to `unique(['user_id','name'])`) — it is redundant, and MySQL uses the composite for both the FK and `user_id` lookups.
- Keep the explicit single-column indexes on `plan_id` / `category_id` (FK + `withCount` need them).

## Columns → indexes → foreign keys, in that order

Define all columns first, then `index()`/`unique()`, then split `foreignId()` + `foreign()->references()` for the FKs. This way MySQL resolves FK indexing against our indexes instead of auto-creating a stray `*_foreign` index — verify with `database-schema` after `migrate:fresh` (a stray auto-index means the order was broken).

## Fold pre-deployment schema changes into the owning migration

While there is no deployment story yet (fresh installs and disposable dev volumes), new columns go into the migration that already owns the table — `password_changed_at` lives inside `0001_01_01_000000_create_users_table.php`, not a dated add-on file. Align databases that already ran the migration with an additive `Schema::table` (tinker) — never `migrate:fresh` on shared dev data. Revisit this preference once a real deployment exists: upgrade paths then need additive files.

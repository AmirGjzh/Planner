---
paths:
  - 'database/seeders/**'
---

# Seeders

Rules for `database/seeders/**`.

## The call list is empty on purpose

- `DatabaseSeeder` has an **empty call list** — `migrate --seed` runs no seeders at all (both dev and the production boot seed are no-ops today). `UserSeeder` is an empty placeholder not wired in (admin user for a future auth/roles phase).
- `SampleDataSeeder` is the demo/screenshot dataset (a known-credential account plus categories, plans, and tasks) and is deliberately **outside** `DatabaseSeeder`'s call graph — run it by hand with `php artisan db:seed --class=SampleDataSeeder`.
- Production's php-fpm entrypoint runs `migrate --seed --force` on **every boot** — never wire `SampleDataSeeder` (or any known-credential/dev seed) into `DatabaseSeeder`; if the call list ever gains a real user-creating seeder, revisit the entrypoint line first (see [`docker/shared-conventions.md`](../skills/main-project/docker/shared-conventions.md)).

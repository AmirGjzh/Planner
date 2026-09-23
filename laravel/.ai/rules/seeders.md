---
paths:
  - 'database/seeders/**'
---

# Seeders

Rules for `database/seeders/**`.

## UserSeeder is an intentional no-op

- `DatabaseSeeder` only calls `UserSeeder`, which is empty on purpose (admin placeholder for a future auth/roles phase).
- Production's php-fpm entrypoint runs `migrate --seed --force` on **every boot** — never add known-credential or dev users here; if this seeder ever creates real users, revisit the entrypoint line first (see `docker/shared-conventions.md`).

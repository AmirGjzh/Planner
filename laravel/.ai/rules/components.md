---
paths:
  - 'resources/views/components/**'
---

# Components

Conventions for Blade components under `resources/views/components/**`.

## Run `view:clear` after moving Blade components

After moving/renaming a Blade component file (or changing which component an `x-` tag resolves to), run `php artisan view:clear`.

Blade caches compiled views in `storage/framework/views` keyed on the raw tag string, so stale compiled files keep resolving old paths even after the files have moved.
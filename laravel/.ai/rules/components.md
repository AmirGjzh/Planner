---
paths:
  - 'resources/views/components/**'
---

# Components

## view:clear after moving Blade components
After moving/renaming Blade component files (or changing which component a x- tag resolves to), run `php artisan view:clear`. Blade caches compiled views in storage/framework/views keyed on the raw tag string, and stale compiled files keep resolving old paths even after files move.

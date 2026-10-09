---
paths:
  - 'resources/views/components/**'
  - 'resources/views/layouts/**'
---

# Components

Conventions for Blade components under `resources/views/components/**` and the app layout that hosts them (`resources/views/layouts/**`).

## Run `view:clear` after moving Blade components

After moving/renaming a Blade component file (or changing which component an `x-` tag resolves to), run `php artisan view:clear`.

Blade caches compiled views in `storage/framework/views` keyed on the raw tag string, so stale compiled files keep resolving old paths even after the files have moved.

## The header owns the app shell's landmarks

`mine/header` is the app layout's only landmark source; keep its structure intact when editing it:

- The root is `<header>`; both nav blocks (desktop links + mobile dropdown) are `<nav aria-label="{{ __('Main navigation') }}">` — two labelled navs are deliberate, exactly one is visible per viewport.
- The skip link (`#main-content`, `sr-only`) is the header's first child and pairs with `layouts/app.blade.php`'s `<main id="main-content">`; neither half works alone.
- The page owns the only `<h1>`: header initials, username, and labels are `<p>` (live `x-text` sync works the same).
- Icon-only controls carry an explicit `aria-label` (`Menu`) — decorative `<x-mine.icon>` children never name a control (mine.md).
- Never build an attribute with `{{ }}`: `nav-link` once emitted `{{ $active ? 'aria-current="page"' : '' }}` and `e()` turned the quotes into `&quot;`, so `aria-current` shipped broken and invisible to tests — use a literal `@if ($active) aria-current="page" @endif` instead.

## Dropdown menus declare themselves

`dropdown/content` carries `role="menu"` because `dropdown.trigger` announces `aria-haspopup="menu"` — keep the pair in sync; `dropdown/divider` is already `role="separator"` and items render `menuitem`/`menuitemcheckbox`. When a menu gains non-item children (the header's profile card is one), re-run the open-menu axe pass in `HeaderJourneyTest` before shipping.

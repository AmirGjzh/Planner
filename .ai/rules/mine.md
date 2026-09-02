---
paths:
  - resources/views/components/mine/brand-logo.blade.php
  - 'resources/views/components/mine/**'
---

# Mine

## Logo color comes from the active theme var
The brand logo is rendered inline by <x-mine.brand-logo> (never as <img>), with fill=currentColor driven by the mine-logo @utility -> color:var(--mine-btn-primary-bg) in mine.css. Do not point to storage/images/logo.svg via <img>: it had a hardcoded #173B67 fill that could not react to theme changes.

## Nested UI components are anonymous Blade, not Livewire
Nested components are anonymous Blade only, under `resources/views/components/mine/` (the `x-mine.*` tag auto-resolves there with no PHP class). There are no class-based components in `app/View/Components` and no `@include` partials. Give each component a `mine/<name>/index.blade.php` entry point plus role-named sub-blades (e.g. `input/label.blade.php`, `dropdown/trigger.blade.php`). State that needs reactivity belongs on the page component, not a nested Livewire component.

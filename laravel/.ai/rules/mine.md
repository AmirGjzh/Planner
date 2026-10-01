---
paths:
  - resources/views/components/mine/brand-logo.blade.php
  - 'resources/views/components/mine/**'
---

# Mine

Conventions for the `mine` UI component family under `resources/views/components/mine/`.

## Brand logo color comes from the active theme variable

The brand logo is rendered inline by `<x-mine.brand-logo>` (never as `<img>`), with `fill=currentColor` driven by the `mine-logo` `@utility` (`color: var(--mine-btn-primary-bg)` in `mine.css`).

Do not point at `resources/images/logo.svg` via `<img>` — it had a hardcoded `#173B67` fill that cannot react to theme changes.

## Nested UI components are anonymous Blade, not Livewire

- Nested components are anonymous Blade only, under `resources/views/components/mine/` — the `x-mine.*` tag auto-resolves there with no PHP class.
- No class-based components in `app\View\Components`, no `@include` partials.
- Each component has a `mine/<name>/index.blade.php` entry point plus role-named sub-blades (e.g. `input/label.blade.php`, `dropdown/trigger.blade.php`).
- State that needs reactivity belongs on the page component, not a nested Livewire component.

## Accessibility contracts live in the components

- `alert` picks its `role` from the variant — `danger`/`warning` → `alert`, `success`/`info` → `status` — through attribute merge, so a caller can override it.
- `input` emits the error `id`, `role="alert"`, `aria-invalid`, and `aria-describedby` wiring itself; the error `id` is the input id + `-error`.
- `Reicon` renders `aria-hidden="true"` by default — decorative unless a caller passes an explicit label.
- Icons/graphics never become the accessible name: `brand-logo` takes `aria-label` from the caller (header passes `config('app.name')`).
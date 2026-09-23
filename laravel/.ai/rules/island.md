---
paths:
  - 'resources/views/pages/**'
  - 'resources/views/components/**'
---

# Island

Rules for the Alpine-driven filter/search/sort island pattern (page-level rendering boost, used in UC-06/07/08).

## Build the filter/search/sort region as an `@island`

- Put `wire:model.live.debounce` (or native `wire:` directives) **inside** the island — Livewire auto-scopes any `wire:` directive whose element sits inside an island, so only the island re-renders.
- Only Alpine `@click` calls must be scoped manually with `$wire.$island('name').$set('prop', value)`.
- Add `always: true` so CRUD actions (parent renders) also refresh the island.
- **Modals must live OUTSIDE the island.** Wrap the list/sort/search in the island — not the modal conditionals.
- Loading feedback: keep the real grid mounted and dim it (`wire:loading.delay.short.class="opacity-40"`) while a centered spinner overlays it (`wire:loading.delay.short` on an `absolute inset-0` wrapper). The container keeps its exact height so pagination doesn't jump. Don't swap a skeleton into the grid.
- Persist filter/sort/search state on the page with `#[Url]` and call `resetPage()` whenever filters change — deep links and the back button stay correct.

## Pitfalls that silently break islands

- `wire:loading.delay.150ms` is **not** a valid v4 delay — Livewire's injected CSS only hides named delays (`delay.short` = 150ms); unknown delays stay visible at rest.
- `wire:target="someProp"` matches payload updates/calls across the whole component, not per-island — omit it inside islands.
- `wire:loading` show/hide sets `display` inline, clobbering `display: grid` on the element itself — keep grid classes on an inner div.
- Don't hand-roll Alpine `x-data`/`x-model`/`wire:ignore` on inputs to fake islands — you'll fight Alpine (e.g. `value` resolving to the DOM node) and lose focus semantics. Native `wire:model.live` auto-scopes by containment.
---
paths:
  - 'resources/views/components/mine/animate/**'
---

# Animate

## Don't use double-quoted PHP interpolation inside @class on x-mine.animate
When wrapping an element in `<x-mine.animate>`, Blade compiles the component tag's attribute data and re-quotes any inline class strings into SINGLE quotes. A double-quoted string like `"self-start {$config['card']} ..."` inside `@class([...])` becomes `'self-start {$config['card']} ...'` in the compiled output, producing a PHP syntax error (unexpected identifier "card"). Fix: move interpolation out of the string — either concatenate (`Arr::toCssClasses([...]) . ' ' . $config['card']`) via `:class`, or resolve `$config['card']` into a variable first. Static strings (no `$config`/interpolation) are safe inside `@class` on the component.

## Keep mine-animate visible across Livewire re-renders (pagination)
`<x-mine.animate>` starts hidden (`mine-animate`, opacity 0) and reveals via `x-intersect.once`. When the wrapping Livewire island re-renders (e.g. pagination changes the list), persistent nodes WITHOUT `wire:key` (toolbar, pagination bar, header) get their server-side class patched back to hidden, but `x-intersect.once` does NOT re-fire because the element is still in view and the node is reused. Cards WITH `wire:key` are replaced wholly, so their fresh `x-intersect` re-fires (they re-animate fine). Fix: in the component's Alpine `init()`, keep a `revealed` flag and run a `MutationObserver` on the element's `class` attribute that re-adds `mine-animate-visible` whenever `revealed` is true and the class was stripped. Only `x-intersect.once` should set `revealed = true` (moving it elsewhere would re-reveal everything on every morph).

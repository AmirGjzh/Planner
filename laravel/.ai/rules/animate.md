---
paths:
  - 'resources/views/components/mine/animate/**'
---

# Animate

Rules for the `mine/animate` component (`<x-mine.animate>`).

## No double-quoted PHP interpolation inside `@class`

Blade re-quotes inline class strings into single quotes when compiling the component tag's attribute data. A double-quoted string such as `"self-start {$config['card']} ..."` inside `@class([...])` compiles to `'self-start {$config['card']} ...'`, producing a PHP syntax error (unexpected identifier "card").

Fix: move interpolation out of the string — concatenate via `:class` (`Arr::toCssClasses([...]) . ' ' . $config['card']`), or resolve `$config['card']` into a variable first. Static strings (no `$config`/interpolation) are safe inside `@class`.

## Keep `mine-animate` visible across Livewire re-renders

`<x-mine.animate>` starts hidden (`mine-animate`, opacity 0) and reveals via `x-intersect.once`. When the wrapping Livewire island re-renders (e.g. pagination changes the list):

- Persistent nodes **without** `wire:key` (toolbar, pagination bar, header) get their server-side class patched back to hidden, but `x-intersect.once` does **not** re-fire (the element is still in view and the node is reused).
- Cards **with** `wire:key` are replaced wholly, so their fresh `x-intersect` re-fires (they re-animate fine).

Fix: in the component's Alpine `init()`, keep a `revealed` flag and run a `MutationObserver` on the element's `class` attribute that re-adds `mine-animate-visible` whenever `revealed` is true and the class was stripped. Only `x-intersect.once` should set `revealed = true` — moving it elsewhere would re-reveal everything on every morph.
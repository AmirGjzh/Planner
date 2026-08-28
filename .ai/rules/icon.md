---
paths:
  - 'resources/views/components/mine/icon/**'
---

# Icon

## x-mine.icon: reicon SSR; `size` prop is authoritative (px)
x-mine.icon renders reicon SVGs SERVER-SIDE (no JS/Alpine) via App\Support\Reicon::svg(). Attrs: `name` (exact reicon PascalCase, e.g. `Check`, `Search`, `Menu`, `Xmark`), `weight` (`outline` default | `filled`), `size` (px, default 24) — sets width/height and is AUTHORITATIVE (no auto `size-*` class injected, so it never conflicts). Pass a Tailwind `size-*`/`w-*`/`h-*` class only to size via CSS (that overrides width). To add an icon: add its PascalCase name to ICONS in scripts/reicon-export.mjs, run `npm run reicon:export`. Unknown names render empty. Do NOT re-add wireui/heroicons or JS icon rendering.

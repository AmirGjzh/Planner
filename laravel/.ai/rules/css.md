---
paths:
  - resources/css/mine.css
---

# CSS

Rules for `resources/css/mine.css` (custom `@utility` definitions).

## mine-animate must not leave a stacking context

The animated card wrapper (`mine-animate`) sits on the same element as `mine-card` (which has `position: relative` + `backdrop-blur-md`) and contains absolutely-positioned `z-50` dropdowns. Any lingering `transform` or `will-change` on the revealed state forces the card into its own stacking context, painting it above siblings so its dropdown renders below adjacent cards.

- The visible state must end at `transform: none` — **not** `translateY(0)`, which also creates a stacking context.
- Avoid `will-change`.

Tailwind v4 emits `@utility` rules not in source order (`.mine-animate` lands after `.mine-animate-visible` in the output), so `mine-animate-visible` needs `!important` on both `opacity: 1` and `transform: none` to reliably win.
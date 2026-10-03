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

## Animate explicit properties, never `transition-all`

- Every `transition-…` in mine.css and the auth-facing components (input, checkbox, button, alert) lists the properties it animates (e.g. `transition-colors transition-[box-shadow,transform]`, `transition duration-200`) — `transition-all` also animates layout properties and repaints on unrelated DOM changes. The remaining mine components (dropdown, select, datepicker, header, textarea, progress) still use `transition-all`; convert them opportunistically.
- Reduced motion is handled by neutralization: the `@media (prefers-reduced-motion: reduce)` block kills `.mine-alert-enter` and the card reveal's transition — motion never ships enabled to those users.

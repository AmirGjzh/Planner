---
paths:
  - resources/css/{mine,app}.css
  - resources/fonts/*.woff2
---

# CSS

Rules for `resources/css/` — mine.css (custom `@utility` definitions) and app.css (font faces).

## mine-animate must not leave a stacking context

The animated card wrapper (`mine-animate`) sits on the same element as `mine-card` (which has `position: relative` + `backdrop-blur-md`) and contains absolutely-positioned `z-50` dropdowns. Any lingering `transform` or `will-change` on the revealed state forces the card into its own stacking context, painting it above siblings so its dropdown renders below adjacent cards.

- The visible state must end at `transform: none` — **not** `translateY(0)`, which also creates a stacking context.
- Avoid `will-change`.

Tailwind v4 emits `@utility` rules not in source order (`.mine-animate` lands after `.mine-animate-visible` in the output), so `mine-animate-visible` needs `!important` on both `opacity: 1` and `transform: none` to reliably win.

## Animate explicit properties, never `transition-all`

- Every `transition-…` in mine.css and the mine components lists the properties it animates (e.g. `transition-[border-color,background-color,box-shadow]`, `transition-colors`, `transition-[opacity,transform]`) — `transition-all` also animates layout properties and repaints on unrelated DOM changes. No component may introduce `transition-all`; the login page test pins its absence.
- Reduced motion is handled by neutralization: the `@media (prefers-reduced-motion: reduce)` block kills `.mine-alert-enter`, the toast enter/leave animations, and the card reveal's transition — motion never ships enabled to those users.

## Web fonts: exactly three Vazirmatn weights

`resources/css/app.css` declares `@font-face` for 400/500/600 (Regular/Medium/SemiBold) — and nothing else. Bold was dropped app-wide in favour of the dominant `font-semibold` usage, so `font-bold` must never appear in markup; semibold is the heaviest class allowed. The reset mail embeds the same trio as `data:` URIs (`.ai/rules/auth.md`), keeping web and mail from ever drifting apart.

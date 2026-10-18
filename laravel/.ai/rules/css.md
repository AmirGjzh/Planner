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

## Card borders never change on hover

`mine-card-danger` and `mine-card-secondary` set their border **color** statically (`--mine-card-danger-border` / `--mine-card-secondary-border`), and no card utility or card markup carries a `hover:border-*` — the accent edges in the page blades stay `border-{l,r}-6 border-{l,r}-(--mine-btn-*-bg)` with the hover fragment stripped (user decision: a hover border shift implied interactivity the cards don't have). The dead `mine-card-{danger,secondary}-border-hover` palette keys were removed from every theme JSON — don't regenerate them; reintroducing a hover border is only allowed as a deliberate, tested interaction.

## The framework's pagination views are deliberately not scanned

Every paginator in the app renders the project-owned view `resources/views/mine/pagination.blade.php` (reached via `->links('mine.pagination')`, contract in mine.md), which `app.css` `@source`s. The old published `resources/views/vendor/livewire/tailwind.blade.php` override is gone — nothing in `vendor/` renders or needs scanning. The framework's own pagination views never render, so `app.css` carries no `@source` for them: scanning would only bake dead (largely `dark:`) utilities into the bundle. Re-add a vendor `@source` if a non-`mine.pagination` paginator ever ships.

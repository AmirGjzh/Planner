---
paths:
  - 'resources/views/components/**'
  - 'resources/views/layouts/**'
---

# Components

Conventions for Blade components under `resources/views/components/**` and the app layout that hosts them (`resources/views/layouts/**`).

## Run `view:clear` after moving Blade components

After moving/renaming a Blade component file (or changing which component an `x-` tag resolves to), run `php artisan view:clear`.

Blade caches compiled views in `storage/framework/views` keyed on the raw tag string, so stale compiled files keep resolving old paths even after the files have moved.

## The header owns the app shell's navigation landmarks

`mine/header` is the app layout's navigation-landmark source (the footer adds the `contentinfo` landmark — see its own section below); keep its structure intact when editing it:

- The root is `<header>`; both nav blocks (desktop links + mobile dropdown) are `<nav aria-label="{{ __('Main navigation') }}">` — two labelled navs are deliberate, exactly one is visible per viewport.
- The skip link (`#main-content`, `sr-only`) is the header's first child and pairs with `layouts/app.blade.php`'s `<main id="main-content">`; neither half works alone.
- The page owns the only `<h1>`: header initials, username, and labels are `<p>` (live `x-text` sync works the same).
- Icon-only controls carry an explicit `aria-label` (`Menu`) — decorative `<x-mine.icon>` children never name a control (mine.md).
- Never *mint* an attribute from `{{ }}` **outside** an attribute: `nav-link` once emitted `{{ $active ? 'aria-current="page"' : '' }}` as raw tag content and `e()` turned the quotes into `&quot;`, so `aria-current` shipped broken and invisible to tests — use a literal `@if ($active) aria-current="page" @endif` instead. Value-position interpolation (`class="{{ $x }}"`) is safe; there `e()` only escapes the value.

## The app shell is one centered column

- The header, `<main>`, and the footer each carry `mx-auto max-w-7xl` (layouts/app.blade.php + the two component roots) — the shell is one centered column that goes full-bleed below the cap. Change the cap in **all three** places at once. Header/footer keep the 8px side gutter the `m-2` cards used to have via `my-2` + `w-[calc(100%_-_1rem)]` (the free space resolves into the auto margins = 0.5rem each, exactly the old gutter); `<main>` keeps `w-full` (it never had side margins).
- Why not `items-center` on the body: the body is a column flex container, so `align-items: center` shrink-wraps every child to content width and collapses `<main>`'s full-bleed page layouts. Auto cross-margins cancel flex stretch too — that's why an explicit width is required alongside `mx-auto`.

## The footer is a themed, state-aware card

`mine/footer` renders the `contentinfo` landmark in `layouts/app.blade.php` after `<main>` — **the auth layout carries no footer** (user decision; it kept the `<main id="main-content" class="flex flex-1 flex-col">` landmark wrapper, and the auth page roots keep `flex-1` instead of `min-h-dvh`, which still centres the card). While the footer is app-layout-only, guests and unverified visitors can never see it (they can't reach app pages) — the guest Sign in/Sign up branch stays in the component for parity but is unreachable.

- Chrome is `mine-card m-2` (header parity); every colour comes from a `--mine-*` theme variable — no slate/hex literals, ever.
- The bottom bar's top border is `border-t-2 border-(--mine-separator-border)/40`: Tailwind v4 removed `border-opacity-*` — the opacity modifier rides the colour itself (compiles to `color-mix`), so don't reach for the v3 utility.
- Logical utilities only (`text-start`, `space-y-`): direction comes from the `<html dir>`; never locale-conditional classes in the footer.
- The middle (Menu nav + Settings) renders in every state; the Settings column carries the auth actions: a signed-in visitor gets Account settings (→ profile) + a Log out button (`mineLogout` Alpine data in a plain `x-data="{{ $logoutData }}"` echo, `mine-text-error` with the link-style focus ring — header logout parity); a guest gets Sign in / Sign up links. The five quick links use `mine-text-link` with a literal `@if aria-current="page"` (see the attribute-minting rule above); they render for guests and unverified visitors too — the `verified` middleware redirects on click.
- `:attr` compiles **on component tags only** — on a plain element a `:x-data="$var"` ships through as the literal string and Alpine evaluates `$var` as JS (`ReferenceError`). Plain elements use the direct echo (`x-data="{{ $var }}"`); value-position interpolation is safe (see the attribute-minting rule above). The header's `:x-data` exists because component-tag attributes extract as raw strings.
- Column headings are `<h2>`: the page owns the only `<h1>`, so the footer must not skip a level.
- Internal links carry `wire:navigate`; the external GitHub link never does (`target="_blank" rel="noopener noreferrer"`). The GitHub card hovers with background only — card borders never change on hover (css.md).
- The md 3-column grid's hand-tuned pull-ups (GitHub card `sm:-top-30`, the bottom bar's negative-margin tuck) are deliberate — retune by eye, not by measurement.
- The © line pairs a literal © glyph (markup) with a translated `:app :year - All rights reserved` segment — no `dir` override, it follows the document direction. The year follows the UI language's calendar: `Jalali::format(now(), 'yyyy')` (Persian digits) for fa, `date('Y')` otherwise; the fa value localizes the app name (پلنر) and — like the EN key — carries no end period. No Alpine state beyond the logout button — the footer is otherwise static.

## Dropdown menus declare themselves

`dropdown/content` carries `role="menu"` because `dropdown.trigger` announces `aria-haspopup="menu"` — keep the pair in sync; `dropdown/divider` is already `role="separator"` and items render `menuitem`/`menuitemcheckbox`. When a menu gains non-item children (the header's profile card is one), re-run the open-menu axe pass in `HeaderJourneyTest` before shipping. An icon-only trigger (the card action menus are all MoreH-only) has no implicit name — the usage site labels it with the entity it acts on: `aria-label="{{ $task->title }}"`, `$plan->name`, `$category->name`.

## Dropdown placement is overflow-driven, per dropdown

`dropdown/content`'s `placement` is physical (`bottom-start`/`bottom-end` → `left-0`/`right-0`), and the menu must never overflow a page edge — so each dropdown's placement is picked by where its trigger sits, not by one uniform locale rule. RTL (fa) mirrors the toolbar, so a trigger lands on the opposite physical edge and the alignment flips with it. Two formulas encode this:

- A trigger on the outer/trailing edge opens its menu toward the page interior: `placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}"`.
- A trigger on the leading edge (the tasks toolbar's Filter + Category, first in a `justify-between` row) opens the other way: `placement="bottom-{{ app()->isLocale('fa') ? 'end' : 'start' }}"`.

The current map (EN value; fa mirrors): categories — Sort `end`; plans — Sort, Filter, Calendar `end`; tasks — Sort, Plan, Calendar `end`, Filter + Category `start`.

Do not "unify" these to a single formula — the mixed formulas are intentional (a uniform parity pass once regressed the two leading-edge tasks dropdowns). Re-check overflow at 390px in both locales before changing any placement.

## Selects keep their ARIA inside the popup

The `x-mine.select` wrapper div carries no ARIA role — the inner `<ul>` is the `role="listbox"` and each `<li>` is the `role="option"`. A `listbox` role on the wrapper made the trigger an illegal listbox child and turned the whole label+button wrapper into an unnamed ARIA input (axe `aria-required-children` + `aria-input-field-name` fired on every visible select once `TasksJourneyTest` opened a modal). The keyboard handlers bind to the x-data root, not to a role — don't move or re-roled either piece.

## Modals name their dialog

- `x-mine.modal` renders `role="dialog" aria-modal="true"` inside an `x-teleport` — pass `labelled-by="<heading-id>"` and put that `id` on the dialog's heading (which lives in the slot) so the dialog has an accessible name; an unset prop renders nothing. The open-dialog axe pass in `DeleteAccountJourneyTest` is the contract.
- Credential and destructive forms use `:close-by-clicking-away="false"` and `:close-by-escaping="false"` — the explicit Cancel/Close button owns dismissal. The shared modal deliberately has no focus trap (app-wide pattern; if that changes, change it once here for every dialog at once).
- Modal bodies are `px-6 py-6 sm:p-8` and footers are `mt-2 flex justify-end gap-4 sm:mt-4` — fleet-wide (plans/categories/tasks); don't invent per-page padding or drop the `sm:` footer step.

## Button label size and control height

- `x-mine.button` renders label size from the `size` prop (`md` → `text-base`, the default; `sm` → `text-sm`); `height` stays orthogonal (default `h-11`). Callers pick `size="sm"` and pass plain slot text — never force the size with a `class="… text-sm"` override (it fights the component's own `text-base` in the cascade) and never wrap the label in `<p class="text-sm">` (`<p>` is flow content, and the slot already sits inside the button's `<span>`).
- Control height: the components default to `h-11`; the auth cards (login/register/forgot/reset/verify) deliberately pass `h-12`. That split is the convention — don't "unify" it and don't invent other heights.
- Card text runs on a three-tier scale: titles at `text-[15px]`; secondary text — descriptions, counters (Progress label, task-count), and link labels (View-tasks) — at `text-[13px]` (`font-medium`); and status badges (Completed/Overdue/Active pills) plus numeric count pills at `text-[12px]`, with card meta badges at `h-8`. Don't push secondary text down to 12px (that's the badge tier) or titles down to 13px.

## No component text renders font-normal

Interactive and informational text in the `mine` family always carries an explicit weight — `font-medium` for option/list/menu/empty-state text, `font-semibold` for titles — set at the component level, never delegated to the caller. Selection and emphasis are communicated with color and background, never with weight (a selected row gets its token colors, not a heavier font). Any new surface with text gets a weight explicitly.

---
paths:
  - 'resources/views/components/mine/{select,datepicker,input}/**'
  - 'resources/views/components/mine/{textarea,calendar}/**'
---

# Select / Datepicker / Input / Textarea / Calendar

Rules for the `x-mine.select`, `x-mine.datepicker`, `x-mine.input`, `x-mine.textarea`, and `x-mine.calendar` components.

## `leftIcon` prop on select and datepicker

`x-mine.select` and `x-mine.datepicker` accept a `leftIcon` prop (a reicon PascalCase name) just like `x-mine.input`.

- select: the prop flows `index -> trigger` via `@aware` and renders inside a leading flex wrapper before the label.
- datepicker: it renders before the label span (`leftIcon` overrides the legacy `showIcon` calendar).
- The icon only appears if its reicon name is in `app/Support/reicon-icons.php` (regenerate via `npm run reicon:export`); an unexported name renders nothing. The generated file is loaded with `require` (opcached) instead of reading+decoding JSON per request.
- `chevron-up-down` is used for the trailing dropdown arrow in both.

## Input CSS lives in mine.css

The input's browser-hack rules (`::-ms-reveal`/`::-ms-clear` hiding, `-webkit-autofill` shadow/text-fill) live in `resources/css/mine.css`, never in a `<style>` block inside `input/index.blade.php` — the block is static, so embedding it made every input instance re-emit the same bytes into the page.

## Field text is one contract — every control stays in lockstep

Input, textarea, the select's trigger/search, and the datepicker's trigger render value text as `text-sm font-medium text-[var(--mine-text-primary)]/80`; placeholders (real `placeholder:` attributes and the triggers' placeholder state) use `--mine-input-placeholder` on the same `font-medium` text. A typography or color change lands in **all** of them at once — never restyle one control alone. `mine.css`'s autofill rule mirrors the same 80% mix so browser-autofilled text renders identical to typed text.

- Leading icons sit 16px before the following text (input/textarea from the wrapper's `pl-4` + the field's `px-4`; select's icon wrapper `pr-4`/`pl-4`; datepicker from icon `mr-2` + span `mx-2`), and field icons are `size="20"`.
- Labels always render through `x-mine.input.label` — input/textarea pass `for`, select/datepicker pass none (their trigger is not a labelable control).
- The treatment extends to list and panel text: select options and the datepicker's day cells, weekday labels, and month/year selects (plus their option rows) render `text-sm font-medium` at the same `/80` primary. Only resting states use the `/80` — selected/active states keep their own tokens (`selected-bg`/`selected-text` pairs, semibold day pills). The `calendar` panel (the dashboard workload grid) speaks the same language: its day cells, weekday labels, and month/year spans mirror the datepicker's.
- The datepicker's month/year mini-selects (`x-mine.datepicker.select`) take `searchable` and mirror `x-mine.select`'s dropdown anatomy exactly: the Magnifier search row sits on top of the panel (outside the scroll area), the option rows live in their own `mine-scrollbar max-h-60` list, and an empty filter shows the same "No results found" row. Filtering is `x-show` against the mini-select's Alpine `search` (reset on close; the search input is autofocused on open, like `x-mine.select`); the datepicker forwards the prop as `searchableMonths` (default off — a dozen rows don't need it) and `searchableYears` (default on). Its trigger carries `data-datepicker-select="{{ label }}"` as a stable handle.
- Search matches digits across scripts: both the query and the option text are normalized to Western digits before comparing (Persian `۰-۹` and Arabic-Indic `٠-٩` count as `0-9` — digits only, letters are never transliterated). Every filter — `x-mine.select` options, the datepicker's month/year rows, and their "No results found" conditions — goes through the `$digits` Alpine magic registered in `resources/js/app.js`. That registration returns the callable (`(el) => (value) => …`) because magics run as property getters; a callback that converts directly throws at call time, which Alpine swallows into a falsy result (every option silently hides).
- Error state = error background + border at rest, ring only on focus/open — never a resting ring. Transitions animate `transition-[border-color,background-color,box-shadow]` so the error background swap is never abrupt.

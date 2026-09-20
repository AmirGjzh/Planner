---
paths:
  - 'resources/views/components/mine/{select,datepicker,input}/**'
---

# Select / Datepicker / Input

Rules for the `x-mine.select`, `x-mine.datepicker`, and `x-mine.input` components.

## `leftIcon` prop on select and datepicker

`x-mine.select` and `x-mine.datepicker` accept a `leftIcon` prop (a reicon PascalCase name) just like `x-mine.input`.

- select: the prop flows `index -> trigger` via `@aware` and renders inside a leading flex wrapper before the label.
- datepicker: it renders before the label span (`leftIcon` overrides the legacy `showIcon` calendar).
- The icon only appears if its reicon name is in `app/Support/reicon-icons.json` (export via `npm run reicon:export`); an unexported name renders nothing.
- `chevron-up-down` is used for the trailing dropdown arrow in both.
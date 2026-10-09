---
paths:
  - 'lang/**'
---

# Lang

Localization conventions for `lang/**`.

## Use JSON string keys with capitalized sentences

- App strings use capitalized natural-language keys in `lang/fa.json`, consumed via `__('My tasks')` / `__(':count days ago', ['count' => ...])`.
- Do **not** use `lang/*/*.php` short keys (`__('messages.welcome')`) for app strings.
- `lang/en/` holds only framework-provided files.

## Keep fa.json grouped into page blocks

- `lang/fa.json` is organized into page blocks separated by exactly one blank line, in this order: Login | Password recovery | Register | Nav | Profile | Categories | Pagination | Plans | Tasks | Reports | Dashboard | Components.
- The trailing Components block holds copy shared across pages by the component family (toast close, loaders, horizontal-scroll buttons); page-specific keys never belong there.
- Insert new keys inside the block of the page (or feature) that uses them — forgot/reset UI and mail copy belong to the Password recovery block.
- Fix a wrong Persian translation in place instead of adding a second key; a key exists once, never duplicated.
- Keep `:count`-style placeholders Western-digit (the existing `:count` translations render `60`, not `۶۰`).

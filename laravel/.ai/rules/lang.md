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
- Validation messages are sentence keys too: each page's `messages()` maps `'field.rule' => __('…')` (the coverage contract lives in pages.md). There is deliberately no `lang/fa/validation.php` — `lang/en/validation.php` is fallback-only, and a rule missing from `messages()` serves its English text in every locale.

## Keep fa.json grouped into page blocks

- `lang/fa.json` is organized into page blocks separated by exactly one blank line, in this order: Login | Password recovery | Register | Nav | Profile | Categories | Pagination | Plans | Tasks | Reports | Dashboard | Errors | Components.
- The trailing Components block holds copy shared across pages by the component family (toast close, loaders, horizontal-scroll buttons); page-specific keys never belong there.
- Insert new keys inside the block of the page (or feature) that uses them — forgot/reset UI and mail copy belong to the Password recovery block.
- Fix a wrong Persian translation in place instead of adding a second key; a key exists once, never duplicated.
- `lang/fa.context.md` mirrors `fa.json` line-for-line (`"Key" → role · usage detail`; role vocabulary in its header). Parity — same keys, same order, one annotation line each — is enforced by `tests/Feature/LangContextTest.php`, so keys are added, renamed, or removed in both files together.
- Keep `:count`-style placeholders Western-digit (the existing `:count` translations render `60`, not `۶۰`).

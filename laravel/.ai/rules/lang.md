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
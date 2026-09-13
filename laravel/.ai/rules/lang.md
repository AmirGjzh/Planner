---
paths:
  - 'lang/**'
---

# Lang

## Use JSON string keys with capitalized sentences
Localize with JSON string keys: keys are capitalized natural-language sentences in `lang/fa.json` and used via `__('My tasks')` / `__(':count days ago', ['count' => ...])`. Do not use `lang/*/*.php` short keys (`__('messages.welcome')`) for app strings; the `lang/en/` subfolder holds only framework-provided files.

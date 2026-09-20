---
paths:
  - config/themes.php
---

# Config

Rules for `config/themes.php`.

## themes.php returns the full theme list; resolution lives in the component

- `config('themes.name')` — flat array of all 8 themes: `ocean, forest, magic, safrron, amber, chocolate, gol-goli, midnight`.
- `config('themes.vars')` — map keyed by theme (`['ocean' => [...], ...]`), each element the theme's JSON minus its `name` key.
- **No fallback/env-resolution logic in config/themes.php.** `name` is used (a) as the allowed-value list for validation (`Rule::in(config('themes.name'))`) and (b) to drive the theme dropdown in `profile.php`.

The active theme per request resolves at the component level in `resources/views/components/mine/theme-vars/index.blade.php`:

```php
config('themes.vars')[auth()->user()?->theme ?? env('APP_THEME', 'forest')]
```

- Keep that fallback pointing at a present theme file (`forest`).
- Never remove a theme from the array without also removing its json/vars — `Rule::in` and the dropdown must stay in sync.
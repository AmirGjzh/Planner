---
paths:
  - config/themes.php
  - config/themes/**
---

# Config

Rules for `config/themes.php` and the theme JSONs under `config/themes/`.

## themes.php returns the full theme list; resolution lives in the component

- `config('themes.name')` — flat array of theme slugs; the current list lives in `config/themes.php`, not here.
- `config('themes.vars')` — map keyed by theme (`['ocean' => [...], ...]`), each element the theme's JSON minus its `name` key.
- `config('themes.default')` — the fallback theme slug, read from `env('APP_THEME', 'forest')` **inside the config file** so `config:cache` bakes it; never call `env()` at runtime (views/actions included) — it returns `null` once config is cached.
- `name` is used (a) as the allowed-value list for validation (`Rule::in(config('themes.name'))`) and (b) to drive the theme dropdown in `profile.php`.

The active theme per request resolves at the component level in `resources/views/components/mine/theme-vars/index.blade.php`:

```php
config('themes.vars')[$theme ?? auth()->user()?->theme ?? config('themes.default')]
```

- The component's optional `theme` prop pins an explicit slug for surfaces that must ignore the account theme — only the auth layout passes it (`config('themes.default')`, see `.ai/rules/auth.md`); everyone else renders with no prop and keeps the fallback below.
- Keep that fallback pointing at a present theme file (`forest`).
- Never remove a theme from the array without also removing its json/vars — `Rule::in` and the dropdown must stay in sync.

---
paths:
  - config/themes.php
---

# Config

## themes.php returns the full theme list; resolution lives in mine/theme-vars
config/themes.php now returns `'name'` as the full, flat array of all 8 present themes (ocean, forest, magic, safrron, amber, chocolate, gol-goli, midnight) and `'vars'` as a map keyed by theme (`['ocean' => [...], ...]`), each element the theme's JSON minus its `name` key. There is NO fallback/env-resolution logic in config/themes.php anymore — `'name'` is used as the allowed-value list for validation (`Rule::in(config('themes.name'))`) and to drive theme dropdowns (see profile.php). The active theme per request is resolved at the component level in `resources/views/components/mine/theme-vars/index.blade.php` as `config('themes.vars')[auth()->user()?->theme ?? env('APP_THEME', 'forest')]` — always keep that fallback pointing at a present theme file (forest), and never remove a theme from the array without also removing its json/vars so `Rule::in` and the dropdown stay in sync.

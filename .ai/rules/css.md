---
paths:
  - 'config/themes.php, config/themes/*.json, resources/views/components/mine/theme-vars.blade.php, resources/css/mine.css'
---

# Css

## Theme vars come from per-theme JSON selected by APP_THEME
CSS theme colors are NOT hardcoded. Each theme lives in config/themes/{name}.json (133 --mine-* vars + a `name` key). config/themes.php resolves the active one from env('APP_THEME', 'blue') and returns ['name','available','vars']. The :root block is rendered into <head> by <x-mine.theme-vars /> (Blade, since static CSS can't read env()). mine.css only keeps @utility rules referencing var(--mine-*). To add/switch a theme: add config/themes/{name}.json, add to the available whitelist, and set APP_THEME in .env.

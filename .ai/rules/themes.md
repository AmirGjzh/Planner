---
paths:
  - 'config/themes/**'
---

# Themes

## Dark theme = {theme}-dark.json + .dark block
Dark mode uses per-theme `{theme}-dark.json` files, loaded into `config('themes.dark_vars')` (name key unset). `theme-vars.blade.php` emits `:root { vars }` + `.dark { dark_vars }`; `<html class="dark">` is toggled by `theme-switcher` and hydrated pre-paint via `theme-init`. Dark files may add extra keys (e.g. `mine-theme-switcher-*`) not present in the light file — ThemeTest expects light+2 keys.

---
paths:
  - resources/views/components/mine/brand-logo.blade.php
---

# Mine

## Logo color comes from the active theme var
The brand logo is rendered inline by <x-mine.brand-logo> (never as <img>), with fill=currentColor driven by the mine-logo @utility -> color:var(--mine-btn-primary-bg) in mine.css. Do not point to storage/images/logo.svg via <img>: it had a hardcoded #173B67 fill that could not react to theme changes.

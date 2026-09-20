---
paths:
  - 'app/Actions/Task/**'
---

# Task

Audit decision for task CRUD (UC-08), partially mirroring the category rules (UC-06).

## Task search is case-insensitive; titles are not normalized

- Search: `whereRaw('LOWER(title) LIKE ?', [Str::lower('%'.$term.'%')])` in the Livewire page.
- Titles are intentionally **not** normalized — tasks have no per-user uniqueness requirement (unlike category/plan names), so casing is kept as entered.
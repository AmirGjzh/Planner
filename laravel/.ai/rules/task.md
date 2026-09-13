---
paths:
  - 'app/Actions/Task/**'
---

# Task

## Task search case-insensitive; no title normalization
UC-08 audit decision (mirrors UC-06/07): Task title search must be case-insensitive and collation-independent - use whereRaw('LOWER(title) LIKE ?', [Str::lower('%'.$term.'%')]) in the Livewire page. Task title normalization is intentionally NOT applied (tasks have no per-user uniqueness requirement, unlike category/plan names).

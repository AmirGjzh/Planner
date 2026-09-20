---
paths:
  - 'app/Actions/Plan/**'
---

# Plan

Audit decisions for plan CRUD (UC-07), mirroring the category rules (UC-06).

## Normalize names only inside Create/Edit Actions

Name normalization (`Str::ucfirst(Str::lower())`) lives only in `CreatePlanAction`/`EditPlanAction`. Pass the raw validated value from the page; never normalize in the Livewire component (`plans.php`).

## Search is case-insensitive and collation-independent

Use `whereRaw('LOWER(name) LIKE ?', [Str::lower('%'.$term.'%')])`.

## Delete, Complete, and Reopen are deliberately not rate-limited

Ownership checks plus the `HasTasks` guard make delete low-risk; Complete/Reopen is a lightweight toggle. Keep them unthrottled.
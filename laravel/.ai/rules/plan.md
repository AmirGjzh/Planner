---
paths:
  - 'app/Actions/Plan/**'
---

# Plan

Audit decisions for plan CRUD (UC-07), mirroring the category rules (UC-06).

## Normalize names only inside Create/Edit Actions

Name normalization (`trim` + edge half-space strip + `Str::ucfirst(Str::lower())`) lives only in `CreatePlanAction`/`EditPlanAction`. The page canonicalizes the fields before validation (the register-username pattern — whitespace-only input must hit the `required` message) and passes the canonical, otherwise raw value; never lowercase/ucfirst in the Livewire component (`plans.php`). `trim()` is byte-wise and blind to ZWNJ, so both layers strip edge `نیم‌فاصله` with profile's unicode regex (`/^[\s\x{200C}]+|[\s\x{200C}]+$/u`) — otherwise a visually identical name dodges the unique index, and a half-space-only name reads as invisible. The description gets profile's `nullableString` edge-strip on both layers (blank → null). Mid-name ZWNJ↔space unification is deliberately deferred to the Normalization pass in `Refactor.md`.

## Search is case-insensitive and collation-independent

Use `whereRaw('LOWER(name) LIKE ?', [Str::lower('%'.$term.'%')])`. The term is `trim()`med once in the computed: storage canonicalizes names, so search must trim too (covers both keystrokes and `?search=` URL entries).

## Done-flag invariant: an undone task reopens a done plan

A `done` plan must contain only done tasks. `ToggleTaskDoneAction` (un-done), `CreateTaskAction`,
and `EditTaskAction` reopen the plan whenever a write would leave an undone task inside a completed
plan — silent side effect with an `info` log; no result-enum change, no page wiring.

## Date-range invariant: task dates stay inside the plan's range

`CreateTaskAction`/`EditTaskAction` return `DateOutOfRange` when `task_date` lies outside the
plan's `start_date`/`finish_date` (checked after `InvalidPlan`; the plan is fetched, not merely
existence-checked, to read its range). The mirror lives in `EditPlanAction`: it returns
`RangeExcludesTasks` when the new range would strand an existing task. ISO `Y-m-d` dates compare
correctly as strings — no timestamp conversion. Both failures hit the limiter like `AlreadyExists`.

## Delete, Complete, and Reopen are deliberately not rate-limited

Ownership checks plus the `HasTasks` guard make delete low-risk; Complete/Reopen is a lightweight toggle. Keep them unthrottled.

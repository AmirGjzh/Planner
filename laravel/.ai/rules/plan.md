---
paths:
  - 'app/Actions/Plan/**'
---

# Plan

Audit decisions for plan CRUD (UC-07), mirroring the category rules (UC-06).

## Normalize names only inside Create/Edit Actions

Name normalization is `Str::squish()` — edge trim plus internal whitespace collapse, **no case folding** — and lives only in `CreatePlanAction`/`EditPlanAction`. The page canonicalizes the name with the same call before validation (the register-username pattern — whitespace-only input must hit the `required` message) and passes the canonical, otherwise raw value; no case handling in the Livewire component (`plans.php`) at all. `Str::squish` = `Str::trim` (its default charlist strips every invisible character incl. `نیم‌فاصله`/ZWNJ — so a visually identical name can't dodge the unique index and a half-space-only name can't render invisible) + collapsing internal `\s` runs to one space. The exact casing the user typed is what gets stored (user decision — the old `Str::ucfirst(Str::lower())` folded case); case-insensitive uniqueness still holds via the `(user_id, name)` unique index plus the ci-collation pre-check. The description gets the plan actions' `nullableString` edge-strip on both layers (blank → null) — multi-line text, so no whitespace merging. Mid-name ZWNJ↔space unification is deliberately not done (user decision — it belongs to the copy pass, tracked in `Refactor.md`).

## Search is case-insensitive and collation-independent

Use `whereRaw('LOWER(name) LIKE ?', [Str::lower('%'.$term.'%')])`. The term is `Str::squish()`ed once in the computed (edge trim + internal whitespace collapse): storage canonicalizes names, so search must canonicalize the term too (covers both keystrokes and `?search=` URL entries).

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

## Progress is time-weighted, not task-count-weighted

A plan card's percentage is `round(done estimated minutes / total estimated minutes * 100)` — both sums selected in the `plans()` computed (`withSum('tasks', 'estimated_minutes')` + the constrained `tasks as tasks_done_sum_estimated_minutes` alias; the plain sum also feeds the "load" sort). Task `estimated_minutes` is `required|min:1`, so a plan that has tasks always has total ≥ 1 — the `> 0` guard only ever catches the 0-task case. The completed-plan arm uses the same sums for its bar ratio (its `100` fallback covers a completed plan with no tasks). Counting tasks (the old math) misweights a 5-minute task equal to a 5-hour one.

## Delete, Complete, and Reopen are deliberately not rate-limited

Ownership checks plus the `HasTasks` guard make delete low-risk; Complete/Reopen is a lightweight toggle. Keep them unthrottled.

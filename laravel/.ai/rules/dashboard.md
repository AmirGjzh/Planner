---
paths:
  - 'app/Actions/Dashboard/**'
---

# Dashboard

Rules and invariants for the dashboard domain (`WeeklyWorkloadAction`, attention list, etc.).

## Week start must stay locale-consistent across dashboard and reports

`WeeklyWorkloadAction`, the report presets, and their tests all assume a locale-aware week:

- **en:** Sunday (`Carbon::SUNDAY` / week starts Sunday).
- **fa:** Saturday (`Carbon::SATURDAY` / `Jalali::weekBounds`).

If either side shifts, the dashboard grid and the "This week" report preset silently disagree.

## WorkloadLevel is pure domain; labels live in Blade

`WorkloadLevel` enum is domain-only: constants plus `forMinutes()`. All human labels are `__()` closures in the dashboard Blade, not in the enum.
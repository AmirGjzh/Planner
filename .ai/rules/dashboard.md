---
paths:
  - 'app/Actions/Dashboard/**'
---

# Dashboard

## Keep locale week starts consistent across dashboard/reports
WeeklyWorkloadAction, reports presets, and their tests all assume the week starts Saturday in fa (Carbon::SATURDAY / Jalali::weekBounds) and Sunday in en. If either side shifts, the dashboard grid and the "This week" report preset silently disagree. Also: WorkloadLevel enum is a pure domain mapping (constants + forMinutes only); all labels live as __() closures in the dashboard blade.

---
paths:
  - 'app/Actions/Plan/**'
---

# Plan

## Plan name normalization + case-insensitive search
UC-07 audit decisions (mirror UC-06): (1) Plan name normalization (Str::ucfirst(Str::lower())) lives ONLY in CreatePlanAction/EditPlanAction - never normalize in the Livewire page (plans.php); pass the raw validated value to the action. (2) Plan search must be case-insensitive and collation-independent: use whereRaw('LOWER(name) LIKE ?', [Str::lower('%'.$term.'%')]). (3) Delete is not rate-limited (ownership + HasTasks guard make it low-risk); Complete/Reopen deliberately not rate-limited.

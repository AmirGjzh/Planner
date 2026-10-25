---
paths:
  - 'app/Actions/Dashboard/**'
  - app/Enums/WorkloadLevel.php
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

## The attention query is bounded, ordered, and trimmed in PHP

`AttentionTasksAction` stays one query: `where('done', false)` + `task_date <= today + 365` (`day_before_alarm`'s validated max — anything further is provably out of window), `orderBy('task_date')->orderBy('id')`, then the exact per-task window math (`task_date − day_before_alarm <= today`) filters in PHP and `values()` reindexes.

- Keep the SQL portable: tests run on SQLite, production on MariaDB — no `DATE()`/`DATE_ADD()` interval expressions. The horizon bound is what makes the PHP trim cheap instead of hydrating every undone task.
- The `id` tiebreaker is the determinism contract: same-date cards must keep a stable order across renders — row order without `ORDER BY` is not guaranteed.
- Query-count locks pin this: exactly one query per action, exactly two on a page render (`dashboard.test.php` + the action tests) — a new query on this page fails them.

## The workload range labels track the enum constants

`WorkloadLevel`'s `*_MAX_MINUTES` constants are the thresholds. The dashboard Blade's `$levelRange` strings (`Light (1–120 min)`) and their `fa.json` keys restate those numbers as copy — they cannot be generated (the digits live inside translated sentences). Change a constant → update the Blade ranges and the `fa.json` sentences (+ `fa.context.md` annotations) in the same change.

## Every rendered count is digit-localized

Numbers the dashboard renders — the attention count badge and the `:count` replacements in `:count days ago` / `in :count days` — pass through the Blade header's `$digit` closure: identity in `en`, `PersianNumber::convert()` in `fa` (the `pagination.blade.php` `$num` precedent). A raw `count()` or int reaching `{{ }}` fails the fa-digit pin in `dashboard.test.php`. Dates need no `$digit`: `Jalali::format` renders through the `fa_IR` locale (Persian digits) and `Minutes::format` is already fa-aware.

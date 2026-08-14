---
name: main-project
description: "Activate this skill at the start of every session — it provides project context, UC numbering, phase progress, and implementation status for the Planner project."
license: MIT
metadata:
  author: planner-team
---

# Planner Project

## Overview

A Laravel 13 + Livewire 4 task planner application. Users manage tasks, categories, and plans with calendar views, workload tracking, and performance reports.
## UC Numbering (Topological Order)

| # | UC | Description |
|---|---|---|
| 01 | Login | Email + password authentication |
| 02 | Register | Username, email, password with validation |
| 03 | View and Edit Profile | View and update personal info |
| 04 | Delete Account | Soft-delete with password confirmation |
| 05 | Logout | Session invalidation with SPA transition |
| 06 | Manage Categories | CRUD with duplicate detection, rate limiting, task-guarded deletion; search + sort (name/tasks/latest) |
| 07 | Manage Plans | CRUD with duplicate detection, rate limiting, task-guarded deletion; complete/reopen, progress bars, search, sort, status filter, calendar |
| 08 | Manage Tasks | CRUD with category/plan validation, rate limiting, toggle done, overdue, date-range calendar, filter & sort, deep-links |
| 09 | Daily Workload | Total estimated time per day with alerts — **Deferred** (dashboard page, not yet built) |
| 10 | Upcoming Tasks | Tasks within notification window — **Deferred** (dashboard page, not yet built) |
| 11 | Reports | Performance stats over a date range — **Deferred** (report page, not yet built) |

### UC Mapping (Old → New)

| Old | New | Fate |
|---|---|---|
| 01–05 Auth/Profile | 01–05 | unchanged |
| 06 Categories | 06 | unchanged |
| 07 Plans | 07 | absorbs old UC-14 (Plan Progress & Tracking) and UC-15 (Filter & Sort) page capabilities |
| 08 Manage Tasks | 08 | merged with old UC-09 (Toggle Done), UC-11 (Overdue), UC-13 (Calendar View), and UC-15 (Filter & Sort) task portions |
| 10 Daily Workload | 09 | renumbered; dashboard page for later |
| 16 Upcoming Tasks | 10 | renumbered; dashboard page for later |
| 12 Reports | 11 | renumbered; report page for later |
| 14 Plan Progress & Tracking | — | folded into UC-07 |
| 15 Filter & Sort | — | folded into UC-06/07/08 (each page documents its own search/sort/filter) |

## Implementation Status

- **Completed:** UC-01, 02, 03, 04, 05, 06, 07, 08
- **Deferred/Future:** UC-09 (Daily Workload), UC-10 (Upcoming Tasks), UC-11 (Reports) — dashboard/report pages not yet built

## Phases

- `phases/phase-0.md` — Vision & Scope
- `phases/phase-1.md` — Requirements (11 UCs, NFRs, ACs)
- `phases/phase-2.md` — Domain Design + UC Specs
- `phases/phase-3.md` — Data Model & Architecture
- `phases/phase-4.md` — Implementation Process & Logs
- `best-practices/Laravel.md` — Laravel Performance & Best Practices Bible
- `best-practices/Livewire.md` — Livewire v4 Performance Tricks

## Key Decisions

- Plan CRUD before Task CRUD (topological dependency)
- UC specs in Phase-2 (design-time), implementation logs in Phase-4
- UC-08 merges task CRUD, toggle done, overdue, calendar/date-range, and filter & sort into one task-management UC
- UC-09/10/11 (Daily Workload, Upcoming Tasks, Reports) target the dashboard/report pages and are deferred

## How to Use

When working on this project, reference the relevant phase file for context. Always check UC numbering before implementing.

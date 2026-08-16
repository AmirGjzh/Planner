---
name: main-project
description: "Activate this skill at the start of every session — it provides project context, UC numbering, phase progress, and implementation status for the Planner project."
license: MIT
metadata:
  author: planner-team
---

# Planner Project

## Overview

A Laravel 13 + Livewire 4 task planner application. Users manage tasks, categories, and plans with date-range views, search/filter/sort, a tasks-needing-attention dashboard with a daily workload grid, and performance reports.

## UC Numbering (Topological Order)

| # | UC | Description |

|---|-----|-------------|
| 01 | Login | Email + password authentication |
| 02 | Register | Username, email, password with validation |
| 03 | View and Edit Profile | View and update personal info |
| 04 | Delete Account | Soft-delete with password confirmation |
| 05 | Logout | Session invalidation with SPA transition |
| 06 | Manage Categories | CRUD with duplicate detection, rate limiting, task-guarded deletion |
| 07 | Manage Plans | CRUD with duplicate detection, rate limiting, task-guarded deletion |
| 08 | Manage Tasks | CRUD, toggle done, date-range, filter/sort — with category/plan validation, rate limiting |
| 09 | Daily Workload | Total estimated time per day with alerts (dashboard) |
| 10 | Tasks Needing Attention | Overdue + tasks whose alarm window has started (dashboard) |
| 11 | Reports | Performance stats over a date range |

## Implementation Status

- **Completed:** UC-01, 02, 03, 04, 05, 06, 07, 08, 09, 10, 11

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
- UC-08 Manage Tasks includes toggle done, date-range filtering, and filter/sort
- UC-09 Daily Workload lives on the dashboard as a weekly grid (includes completed tasks)
- UC-10 is a "Tasks Needing Attention" list on the dashboard (overdue + alarm-window-started, excludes done)
- Reports (UC-11) is the last use case — all docs are structured into 11 UC-aligned parts

## How to Use

When working on this project, reference the relevant phase file for context. Always check UC numbering before implementing.

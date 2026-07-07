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
|---|-----|-------------|
| 01 | Login | Email + password authentication |
| 02 | Register | Username, email, password with validation |
| 07 | View and Edit Profile | View and update personal info |
| 08 | Logout | Session invalidation with SPA transition |
| 09 | Delete Account | Soft-delete with password confirmation |
| 03 | Manage Categories | CRUD with duplicate detection, rate limiting, task-guarded deletion |
| 04 | Manage Plans | CRUD with duplicate detection, rate limiting, task-guarded deletion |
| 10 | Manage Tasks | CRUD with category/plan validation, rate limiting |
| 13 | Toggle Done | Mark task done/not done |
| 14 | Daily Workload | Total estimated time per day with alerts |
| 19 | Calendar View | Date range filtering (daily/weekly/monthly deferred) |
| 16 | Overdue Tasks | Tasks past due date and not done |
| 25 | Upcoming Tasks | Tasks within notification window |
| 24 | Filter & Sort | By category, plan, status, priority, date |
| 18 | Reports | Performance stats over a date range |
| 21 | Plan Progress & Tracking | Task list, completion %, real-time tracking |

## Implementation Status

- **Completed:** UC-01, 02, 03, 04, 07, 08, 09, 10, 13, 14, 18, 19, 21, 24, 25
- **Deferred:** UC-16

## Phases

- `phases/phase-0.md` — Vision & Scope
- `phases/phase-1.md` — Requirements (16 UCs, NFRs, ACs)
- `phases/phase-2.md` — Domain Design + UC Specs
- `phases/phase-3.md` — Data Model & Architecture
- `phases/phase-4.md` — Implementation Process & Logs
- `best-practices/Laravel.md` — Laravel Performance & Best Practices Bible
- `best-practices/Livewire.md` — Livewire v4 Performance Tricks

## Key Decisions

- Plan CRUD before Task CRUD (topological dependency)
- UC specs in Phase-2 (design-time), implementation logs in Phase-4

## How to Use

When working on this project, reference the relevant phase file for context. Always check UC numbering before implementing.

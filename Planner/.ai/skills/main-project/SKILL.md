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
| 02 | Register | Username, email, password |
| 03 | Categories | CRUD, each task belongs to one |
| 04 | Create Plan | Name, description, date range |
| 05 | Edit Plan | Modify plan details |
| 06 | Delete Plan | Blocked if plan has tasks |
| 07 | Profile | View & edit user info |
| 08 | Logout | Session invalidation |
| 09 | Delete Account | Soft delete with password confirmation |
| 10 | Create Task | Day, title, category, duration, priority |
| 11 | Edit Task | Modify all task fields |
| 12 | Delete Task | Hard delete |
| 13 | Toggle Done | Mark done/not done |
| 14 | Workload | Total estimated time per day |
| 15 | Colors | Day status indicators (White/Green/Yellow/Red/Black) |
| 16 | Overdue | Tasks past due date, not done |
| 17 | Auto Not-Done | End-of-day status for reporting |
| 18 | Reports | Performance stats over date range |
| 19 | Calendar | Daily/weekly/monthly views |
| 20 | Day Details | Click day → daily view |
| 21 | View Plan Tasks | Tasks assigned to a plan |
| 22 | Plan Progress | Completion percentage |
| 23 | Progress Tracking | Real-time plan updates |
| 24 | Filter/Sort | By category, plan, status, priority, date |
| 25 | Upcoming | Tasks within X days |
| 26 | Recurring | Multi-day task assignment |
| 27 | Soft Delete | User soft-delete, cascade for plans/tasks |

## Implementation Status

- **Completed:** UC-01 through UC-15, UC-19, UC-21 through UC-24
- **Remaining:** UC-16 through UC-18, UC-20, UC-25 through UC-27

## Phases

- `phases/phase-0.md` — Vision & Scope
- `phases/phase-1.md` — Requirements (27 UCs, NFRs, ACs)
- `phases/phase-2.md` — Domain Design + UC Specs
- `phases/phase-3.md` — Data Model & Architecture
- `phases/phase-4.md` — Implementation Process & Logs

## Key Decisions

- Plan CRUD before Task CRUD (topological dependency)
- UC specs in Phase-2 (design-time), implementation logs in Phase-4
- Sheaf UI deferred to Layer 2 (no Boost skill yet)
- Laravel.md and Livewire.md deferred to Layer 2

## How to Use

When working on this project, reference the relevant phase file for context. Always check UC numbering before implementing.

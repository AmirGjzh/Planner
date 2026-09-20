---
name: main-project
description: "Activate at the start of every session — project context, UC numbering, implementation status, key decisions, and the docs map (phases, docker, git workflow) for Planner."
license: MIT
metadata:
  author: planner-team
---

# Planner Project

## Overview

A Laravel 13 + Livewire 4 task planner. Users manage tasks, categories, and plans with date-range views, search/filter/sort, a tasks-needing-attention dashboard with a daily workload grid, and performance reports. All 11 use cases are delivered, with Persian/English support and 8 color themes.

## When to use this skill

Activate at the start of every session (or lazily when working on Planner). It provides:

- the **UC map** and implementation status below,
- the **key decisions** that must not regress,
- pointers to the phase, docker, and git docs.

Pair it with the project rules in `.ai/rules/` (see the mandatory ritual in the guideline: read `rules/index.md` and every matching rule file before planning or editing).

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
| 08 | Manage Tasks | CRUD, toggle done, date-range, filter/sort — category/plan validation, rate limiting |
| 09 | Daily Workload | Total estimated time per day with alerts (dashboard) |
| 10 | Tasks Needing Attention | Overdue + tasks whose alarm window has started (dashboard) |
| 11 | Reports | Performance stats over a date range |

## Implementation Status

- **Completed:** UC-01, 02, 03, 04, 05, 06, 07, 08, 09, 10, 11
- Working/verified state is summarized per use case in `phases/Phase-4.md`.

## Docs Map

| Path | Contents |
| --- | --- |
| `phases/Phase-0.md` | Vision & scope |
| `phases/Phase-1.md` | Requirements (11 UCs, NFRs, acceptance criteria, MoSCoW) |
| `phases/Phase-2.md` | Conceptual domain design + per-UC flows |
| `phases/Phase-3.md` | Data model & architecture (schema, patterns, error/logging policy) |
| `phases/Phase-4.md` | Implementation process (build/test/refactor/doc/accept) + delivered scope |
| `docker/overview.md` | Shared docker conventions (healthchecks, env contract, wrappers) |
| `docker/development.md` | Dev workspace stack, runbook, troubleshooting |
| `docker/production.md` | Prod nginx/php-fpm/mysql/redis stack, runbook, troubleshooting |
| `git-setup/setup.md` | Two-remote git workflow (public `origin` / private `private`) |

## Key Decisions

- **Plan CRUD before Task CRUD** (topological dependency).
- **UC specs** live in Phase-2 (design-time); delivered scope + durable decisions in Phase-4; the dated per-UC implementation journal was moved to git history.
- **UC-08 Manage Tasks** includes toggle-done, date-range filtering, and filter/sort.
- **UC-09 Daily Workload** lives on the dashboard as a weekly grid and **includes** completed tasks.
- **UC-10 "Tasks Needing Attention"** = overdue + alarm-window-started, **excludes** done tasks.
- **Reports (UC-11)** are the last use case; all docs are structured into 11 UC-aligned parts.
- **Locale-aware week:** en starts Sunday, fa starts Saturday (Jalali); enforced across the dashboard grid and report presets (see `rules/dashboard.md`, `rules/tests.md`).
- **Per-user language preference:** `users.locale` (fa/en) set on the profile page (UC-03); the shared `HasUser` trait's `boot()` applies it app-wide on every Livewire request. Guest pages use the app/.env locale; only auth boundaries set the locale explicitly.
- **Rate limiting is action-level**, keys via `Str::transliterate('kebab-prefix:identifier')`, guest vs authenticated shapes; cleared on success (see `rules/actions.md`).
- **Soft-delete only for User** with credential obfuscation (`deleted-user-{id}`); other entities use restrict-on-delete.
- **Ownership enforced in Actions, not Livewire** — frontend-agnostic defense in depth.

## How to Use

Reference the relevant phase file and the matching `.ai/rules/*.md` before implementing. Always check UC numbering before starting work. For generic Laravel/Livewire/Tailwind/testing patterns, use the corresponding boost agent skills and the `search-docs` MCP tool; this skill only carries Planner-specific knowledge.
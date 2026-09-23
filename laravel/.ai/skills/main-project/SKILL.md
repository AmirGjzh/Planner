---
name: main-project
description: "Activate at the start of every session — project context, UC numbering, implementation status, key decisions, and the docs map (phases, docker, git workflow, agentic environment) for Planner."
license: MIT
metadata:
  author: planner-team
---

# Planner Project

## Overview

A Laravel 13 + Livewire 4 task planner. Users manage tasks, categories, and plans with date-range views, search/filter/sort, a tasks-needing-attention dashboard with a daily workload grid, and performance reports. All use cases are delivered, with Persian/English support and swappable color themes.

## When to use this skill

Activate at the start of every session (or lazily when working on Planner). It provides:

- the **UC map** and implementation status below,
- the **key decisions** that must not regress,
- pointers to the phase, docker, git, and agentic-environment docs.

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
- Working/verified state is summarized per use case in `phases/phase-4-implementation.md`.

## Docs Map

| Path | Contents |
| --- | --- |
| `phases/phase-0-vision-scope.md` | Vision & scope |
| `phases/phase-1-requirements.md` | Requirements (UCs, NFRs, acceptance criteria, MoSCoW) |
| `phases/phase-2-domain-design.md` | Conceptual domain design + per-UC flows |
| `phases/phase-3-detailed-design.md` | Data model & architecture (schema, patterns, error/logging policy) |
| `phases/phase-4-implementation.md` | Implementation process (build/test/refactor/doc/accept) + delivered scope |
| `docker/shared-conventions.md` | Shared docker conventions (healthchecks, env contract, wrappers) |
| `docker/development.md` | Dev workspace stack, runbook, troubleshooting |
| `docker/production.md` | Prod nginx/php-fpm/mysql/redis stack, runbook, troubleshooting |
| `git-setup/workflow.md` | Two-remote git workflow (public `origin` / private `private`) |
| `agentic/setup.md` | Agentic environment: Boost install, opencode MCP bridge, skills via skills.sh, project rules |

## Key Decisions

Project-wide decisions that must not regress; per-UC scope decisions live in `phases/phase-4-implementation.md` under each UC's Key decisions.

- **Build order is topological:** Plan CRUD (UC-07) before Task CRUD (UC-08); Reports (UC-11) last.
- **UC specs** live in Phase-2 (design-time); delivered scope + durable decisions in Phase-4; the dated per-UC implementation journal was moved to git history.
- **Locale-aware week:** en starts Sunday, fa starts Saturday (Jalali); enforced across the dashboard grid and report presets (see `.ai/rules/dashboard.md`, `.ai/rules/tests.md`).
- **Per-user language preference:** `users.locale` (fa/en) set on the profile page (UC-03); the shared `HasUser` trait's `boot()` applies it app-wide on every Livewire request. Guest pages use the app/.env locale; only auth boundaries set the locale explicitly.
- **Rate limiting is action-level**, keys via `Str::transliterate('kebab-prefix:identifier')`, guest vs authenticated shapes; cleared on success (see `.ai/rules/actions.md`).
- **Soft-delete only for User** with credential obfuscation (`deleted-user-{id}`); other entities use restrict-on-delete.
- **Ownership enforced in Actions, not Livewire** — frontend-agnostic defense in depth.

## How to Use

Reference the relevant phase file and the matching `.ai/rules/*.md` before implementing. Always check UC numbering before starting work. For generic Laravel/Livewire/Tailwind/testing patterns, use the corresponding boost agent skills and the `search-docs` MCP tool; this skill only carries Planner-specific knowledge.
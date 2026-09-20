# Phase 3 – Detailed Design

Phase 3 covers three areas of design:

1. **Logical Data Model** — Tables, columns, keys, constraints, enums, factories, seeders
2. **Architecture Principles and Patterns** — Conventions and patterns to follow during implementation
3. **Error Handling and Logging Principles** — Error types, log levels, visibility policy

---

## 3.1 – Logical Data Model

### Overview

The database layer consists of 4 models, 4 migrations, 2 enums, 4 factories, and 1 seeder. All entities are user-scoped — each user owns their own tasks, categories, and plans.

### Entity Summary

| Entity | Table | Primary Key | Soft Delete | Factory | Seeder |
|--------|-------|-------------|-------------|---------|--------|
| User | `users` | `id` | Yes | `UserFactory` | `UserSeeder` |
| Category | `categories` | `id` | No | `CategoryFactory` | — |
| Task | `tasks` | `id` | No | `TaskFactory` | — |
| Plan | `plans` | `id` | No | `PlanFactory` | — |

### Entity-Relationship Diagram

```
User (1) ——— (N) Task
User (1) ——— (N) Category
User (1) ——— (N) Plan
Category (1) ——— (N) Task
Plan (0..1) ——— (0..N) Task
```

### Foreign Key Conventions

| Foreign Key | On Delete Behavior | Rationale |
|-------------|--------------------|-----------|
| `user_id` on all child tables | `cascadeOnDelete` | Deleting a user removes all their data |
| `plan_id` on `tasks` | `restrictOnDelete` | Cannot delete a plan that still has tasks |
| `category_id` on `tasks` | `restrictOnDelete` | Cannot delete a category that still has tasks |

### Tables and Columns

#### `users`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | `bigint unsigned` | Primary key, auto-increment |
| `username` | `string` | Unique, not null |
| `email` | `string` | Unique, not null |
| `password` | `string` | Not null |
| `firstname` | `string` | Nullable |
| `lastname` | `string` | Nullable |
| `birthday` | `date` | Nullable |
| `country` | `string` | Nullable |
| `gender` | `enum('male', 'female')` | Nullable |
| `locale` | `enum('en', 'fa')` | Not null, default `fa` (stored as lowercase string, not cast) |
| `theme` | `enum('ocean','magic','forest','chocolate','amber','safrron','midnight','gol-goli')` | Not null, default `ocean` (stored as string, not cast) |
| `deleted_at` | `timestamp` | Soft delete |
| `email_verified_at` | `timestamp` | Nullable |
| `remember_token` | `string` | Nullable |
| `created_at` / `updated_at` | `timestamp` | Auto-managed |

**Model details:**
- `#[Fillable]`: `username`, `email`, `password`, `firstname`, `lastname`, `birthday`, `country`, `gender`, `locale`, `theme`
- `#[Hidden]`: `password`, `remember_token`
- **Casts:** `email_verified_at` → `datetime`, `birthday` → `date:Y-m-d`, `gender` → `UserGender` enum, `password` → `hashed`
- **Accessors:** `fullName()` — trimmed `"{firstname} {lastname}"`; `initials()` — multibyte-safe ZWNJ-separated initials from firstname+lastname (falls back to username)
- **Relationships:** `plans()` (HasMany), `tasks()` (HasMany), `categories()` (HasMany)
- **Soft deletes:** Yes (only entity with this)
- **Locale note:** `locale` (`fa`/`en`) selects the UI language and is read by the shared `HasUser` trait `boot()`; `theme` selects the CSS variable set resolved by `mine/theme-vars`. See `.ai/rules/factories.md` and `.ai/rules/config.md`.

#### `categories`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | `bigint unsigned` | Primary key, auto-increment |
| `name` | `string` | Unique, not null |
| `user_id` | `bigint unsigned` | Foreign key → `users.id`, cascade on delete |
| `created_at` / `updated_at` | `timestamp` | Auto-managed |

**Model details:**
- `#[Fillable]`: `name`, `user_id`
- **Relationships:** `user()` (BelongsTo), `tasks()` (HasMany)

#### `tasks`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | `bigint unsigned` | Primary key, auto-increment |
| `title` | `string` | Not null |
| `description` | `text` | Nullable |
| `task_date` | `date` | Not null, indexed |
| `estimated_minutes` | `integer` | Not null |
| `priority` | `enum('low', 'medium', 'high')` | Default `'medium'` |
| `done` | `boolean` | Default `false` |
| `day_before_alarm` | `integer` | Default `0` |
| `user_id` | `bigint unsigned` | Foreign key → `users.id`, cascade on delete |
| `plan_id` | `bigint unsigned` | Foreign key → `plans.id`, restrict on delete, nullable |
| `category_id` | `bigint unsigned` | Foreign key → `categories.id`, restrict on delete |
| `created_at` / `updated_at` | `timestamp` | Auto-managed |

**Model details:**
- `#[Fillable]`: all columns above
- **Casts:** `task_date` → `date:Y-m-d`, `estimated_minutes` → `integer`, `priority` → `TaskPriority` enum, `done` → `boolean`, `day_before_alarm` → `integer`
- **Relationships:** `user()` (BelongsTo), `plan()` (BelongsTo), `category()` (BelongsTo)

#### `plans`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | `bigint unsigned` | Primary key, auto-increment |
| `name` | `string` | Unique, not null |
| `description` | `text` | Nullable |
| `start_date` | `date` | Not null |
| `finish_date` | `date` | Not null |
| `done` | `boolean` | Default `false` |
| `user_id` | `bigint unsigned` | Foreign key → `users.id`, cascade on delete |
| `created_at` / `updated_at` | `timestamp` | Auto-managed |

**Model details:**
- `#[Fillable]`: `name`, `description`, `start_date`, `finish_date`, `done`, `user_id`
- **Casts:** `start_date` → `date:Y-m-d`, `finish_date` → `date:Y-m-d`, `done` → `boolean`
- **Relationships:** `user()` (BelongsTo), `tasks()` (HasMany)

### Enums

#### `App\Enums\TaskPriority`

```php
enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
```

Used by `Task.priority` column with automatic cast.

#### `App\Enums\UserGender`

```php
enum UserGender: string
{
    case Male = 'male';
    case Female = 'female';
}
```

Used by `User.gender` column with automatic cast.

### Soft Delete Policy

- **Only `User`** uses soft deletes (`SoftDeletes` trait + `deleted_at` column).
- **Category, Task, and Plan** are hard-deleted immediately.
- Deleting a Plan is blocked if it still has Tasks (restrictOnDelete).
- Deleting a Category with active tasks is rejected by the database (`restrictOnDelete`).

### Factories

| Factory | Key Details |
|---------|-------------|
| `UserFactory` | Cached `Hash::make('password')`, `UserGender::cases()` for gender, fixed `locale='en'` (deterministic tests), random theme from the 8 present themes |
| `CategoryFactory` | `fake()->unique()->word()` for name, inline `User::factory()` |
| `TaskFactory` | `task_date` ≥ today, `estimated_minutes` 1–1200, `TaskPriority::cases()` for priority, nested User/Plan/Category factories |
| `PlanFactory` | `start_date` ±1 month, `finish_date` closure depends on `start_date` + up to 3 months |

### Seeders

- `DatabaseSeeder` — calls `UserSeeder`
- `UserSeeder` — currently empty (admin user placeholder for after auth/roles)

---

## 3.2 – Architecture Principles and Patterns

This section defines the conventions and patterns to follow during implementation. These are guiding principles — the actual services, actions, and components are created as each use case is implemented.

### Code Organization

```
app/
├── Actions/           # Domain actions, grouped by domain
│   ├── Auth/          #   Login, Register, Logout, DeleteAccount
│   ├── Category/
│   ├── Dashboard/     #   WeeklyWorkloadAction, AttentionTasksAction
│   ├── Plan/
│   ├── Profile/
│   ├── Reports/       #   ReportsAction
│   └── Task/
├── Enums/             # Backed enums: XxxResult, TaskPriority, UserGender, WorkloadLevel
├── Http/
│   └── Controllers/   # Base Controller.php only (no route controllers — Livewire pages)
├── Livewire/
│   └── Concerns/      # HasUser trait (shared, applies users.locale via boot())
├── Models/            # Eloquent models
├── Policies/          # CategoryPolicy, PlanPolicy, TaskPolicy (auto-discovered)
├── Support/           # Jalali, Minutes, PersianNumber, Reicon helpers
└── Providers/
```

Full pages are Livewire 4 multi-file components under `resources/views/pages/⚡<name>/` (not `app/Livewire`); nested `mine` UI components are anonymous Blade under `resources/views/components/mine/`.

### Layer Separation Principle

The system follows a simple layered architecture:

```
Livewire page (UI state + user interaction)
       ↓
Action (business logic — final class, execute())
       ↓
Model (data access via Eloquent)
```

**Rules:**
- **Livewire pages** handle UI state, validation, and user interaction; they delegate domain logic to Actions. Search/filter queries and validation stay in the page; anything with side effects or authorization goes through an Action.
- **Actions** are the single domain layer: one `final class` per operation with a single public `execute()` (or domain-named) method, `LoggerInterface` as the only injected dependency, and an `XxxResult` enum for every outcome. No service layer, no repositories, no DTOs, no events/queued jobs (see `.ai/rules/actions.md`).
- **Models** handle data access only — fillable via `#[Fillable]`, casts via `casts()` (see `.ai/rules/models.md`).

### Pattern Decisions

| Pattern | Decision | When to Apply |
|---------|----------|---------------|
| **Action Classes** | Use (only) | Every operation with a side effect is an Action — never a service method. |
| **Service Layer** | Not used | Actions replace services; the term "module" in Phase-2 maps to an `app/Actions/<Domain>/` folder. |
| **Read-only query actions** | Use | UC-09/10/11 are query-only Actions (`WeeklyWorkloadAction`, `AttentionTasksAction`, `ReportsAction`) invoked from page `#[Computed]` methods. |
| **Backed Enums** | Use | Already in place for fixed value sets. Extend as new value sets appear. |
| **Repository** | Defer | Not used — query/filter logic stays as inline Eloquent builder chains in Actions. |
| **Form Request** | Defer | Livewire pages validate natively via `rules()` and `$this->validate()`. |
| **View Composer** | Defer | Format data directly in Livewire page properties/helpers. |

### Naming Conventions

| Layer | Naming Pattern | Example |
|-------|---------------|---------|
| Action | `{Verb}{Noun}Action` | `CreateTaskAction`, `ToggleTaskDoneAction` |
| Action (result) | `{Verb}{Noun}Result` (enum) | `CreateTaskResult::Created` |
| Livewire (full page) | `pages::<name>` SFC | `resources/views/pages/⚡tasks/` |
| Livewire (concern) | `{Domain}` trait | `HasUser` |
| Policy | `{Model}Policy` | `TaskPolicy` |
| Support helper | `{Domain}` class | `Jalali`, `PersianNumber` |

### Service Boundaries

Every use case maps to one `app/Actions/<Domain>/` folder. There is no separate service layer — these boundaries are what the Actions implement.

| UC | Use Case | Module / Service | Responsibilities |
|----|----------|------------------|------------------|
| 01 | Login | Authentication (`LoginUserAction`) | Verify credentials, start session, rate-limit attempts |
| 02 | Register | Authentication (`RegisterUserAction`) | Create account, enforce unique username/email |
| 03 | View and Edit Profile | Profile (`UpdateProfileAction`) | Read/update profile fields, rate-limit updates |
| 04 | Delete Account | Account (`DeleteAccountAction`) | Verify password, obfuscate credentials, soft-delete |
| 05 | Logout | Authentication (`LogoutUserAction`) | Invalidate session, regenerate CSRF token |
| 06 | Manage Categories | Category Management (Create/Edit/DeleteCategoryAction) | Category CRUD, delete protection check |
| 07 | Manage Plans | Plan Management (Create/Edit/Delete/Complete/ReopenPlanAction) | Plan CRUD, progress calculation, delete protection check |
| 08 | Manage Tasks | Task Management (Create/Edit/Delete/ToggleTaskDoneAction) | Task CRUD, toggle done, filter/sort, date range |
| 09 | Daily Workload | Dashboard (`WeeklyWorkloadAction`) | Weekly daily-total calculation, workload level, h/m formatting |
| 10 | Tasks Needing Attention | Dashboard (`AttentionTasksAction`) | Query overdue + tasks within their notification window |
| 11 | Reports | Reporting (`ReportsAction`) | Aggregate tasks/plans performance + per-day workload chart over a date range |

> **Note:** UC-09, UC-10, and UC-11 are implemented as read-only query actions (`WeeklyWorkloadAction`, `AttentionTasksAction`, `ReportsAction`), invoked from page `#[Computed]` methods. No service layer is required for read-only aggregations.

### When to Create an Action

Every operation with a side effect or an authorization boundary is an Action (write once, follow the shape). Read-only aggregation also becomes an Action when it needs its own outcome/visibility (UC-09/10/11). There is no service-method alternative — create the Action and route it from the page component.

---

## 3.3 – Error Handling and Logging Principles

This section defines the conventions for handling errors and logging. Actual exception classes and logger calls are added during implementation.

### Error Types

| Type | How It Is Raised | Example |
|------|-----------------|---------|
| **Validation error** | Livewire `$this->validate()` | Missing title, past date |
| **Authorization failure** | `abort_unless($user->can(...), 403)` inside Actions | Editing another user's task |
| **Domain outcome** | `XxxResult` enum returned from an Action | Delete blocked because the plan still has tasks |
| **System error** | PHP or Laravel exception | Database connection lost |

### Domain Outcome Convention

Expected business-rule failures are **not** exceptions — Actions return an `XxxResult` enum covering every outcome (success, failure, rate-limited) and the page reacts to the result. `app/Exceptions` holds Laravel's defaults; no custom domain exception classes. Delete-protection is enforced by policies (`$user->can('delete', $category)`) and the DB `restrictOnDelete` constraint, then reported through the Action's result enum.

### Errors per Use Case

| UC | Use Case | Expected Validation / Domain Errors |
|----|----------|-------------------------------------|
| 01 | Login | Invalid credentials, rate-limited |
| 02 | Register | Username taken, email taken, rate-limited |
| 03 | View and Edit Profile | Username taken, rate-limited |
| 04 | Delete Account | Wrong password, rate-limited |
| 05 | Logout | None (idempotent — guests are redirected) |
| 06 | Manage Categories | Duplicate name, empty/max-length name, rate-limited, category has tasks |
| 07 | Manage Plans | Duplicate name, invalid date range, rate-limited, plan has tasks |
| 08 | Manage Tasks | Missing/invalid fields, invalid category, invalid plan, rate-limited |
| 09 | Daily Workload | None (computed, read-only) |
| 10 | Tasks Needing Attention | None (computed, read-only) |
| 11 | Reports | None (computed, read-only) |

### Handling Strategy

| Layer | How to Handle |
|-------|---------------|
| **Livewire Page** | Act on the Action's `XxxResult` enum → show a user-friendly message via `session()->flash('toast', ...)` / `$this->dispatch('toast', ...)`; validation → automatic per-field messages. |
| **Action** | Return result enums for all outcomes (success, failure, rate-limited). Do not throw exceptions for expected failures. |

### Logging Conventions

| Level | When to Log |
|-------|-------------|
| `info` | Successful operations (task created, plan deleted, etc.) |
| `warning` | Failed attempts (validation failures, unauthorized access) |
| `error` | Unexpected system errors, database failures, service exceptions |

- Use `Log::info()`, `Log::warning()`, `Log::error()` inside Actions (via the injected `LoggerInterface`).
- Do not log in Livewire pages — delegate to the Action layer.
- Local development uses stack logging; production uses daily files.

### Error Visibility for Users

| Error Type | User Sees |
|------------|-----------|
| Validation error | Per-field message (Livewire handles this) |
| Authorization failure | "You are not authorized to perform this action." |
| Delete blocked (category/plan has tasks) | User-friendly explanation: "This category still has tasks. Reassign or delete them first." |
| Unexpected system error | "Something went wrong. Please try again." |

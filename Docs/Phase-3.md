# Phase 3 – Detailed Design

Phase 3 covers three areas of design:

1. **Logical Data Model (Completed)** — Tables, columns, keys, constraints, enums, factories, seeders
2. **Architecture Principles & Patterns (Overview)** — Conventions and patterns to follow during implementation
3. **Error Handling & Logging Principles (Overview)** — Error types, log levels, visibility policy

---

## 3.1 — Logical Data Model (Completed)

### Overview

The database layer consists of 4 models, 4 migrations, 2 enums, 4 factories, and 2 seeders.
All entities are user-scoped — each user owns their own tasks, categories, and plans.

### Entity Summary

| Entity | Table | Primary Key | Soft Delete | Factory | Seeder |
|--------|-------|-------------|-------------|---------|--------|
| User | `users` | `id` | ✅ Yes | `UserFactory` | `UserSeeder` |
| Category | `categories` | `id` | ❌ No | `CategoryFactory` | — |
| Task | `tasks` | `id` | ❌ No | `TaskFactory` | — |
| Plan | `plans` | `id` | ❌ No | `PlanFactory` | — |

### Entity-Relationship Diagram (Textual)

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
| `plan_id` on `tasks` | `cascadeOnDelete` | Deleting a plan also removes all its tasks |
| `category_id` on `tasks` | `restrictOnDelete` | Cannot delete a category that still has tasks |

### Tables & Columns

#### `users`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | `bigint unsigned` | Primary key, auto-increment |
| `user_name` | `string` | Unique, not null |
| `email` | `string` | Unique, not null |
| `password` | `string` | Not null |
| `first_name` | `string` | Nullable |
| `last_name` | `string` | Nullable |
| `birth_date` | `date` | Nullable |
| `country` | `string` | Nullable |
| `gender` | `enum('male', 'female')` | Nullable |
| `deleted_at` | `timestamp` | Soft delete |
| `email_verified_at` | `timestamp` | Nullable |
| `remember_token` | `string` | Nullable |
| `created_at` / `updated_at` | `timestamp` | Auto-managed |

**Model details:**
- `#[Fillable]`: `user_name`, `email`, `password`, `first_name`, `last_name`, `birth_date`, `country`, `gender`
- `#[Hidden]`: `password`, `remember_token`
- **Casts:** `email_verified_at` → `datetime`, `birth_date` → `date:Y-m-d`, `gender` → `UserGender` enum, `password` → `hashed`
- **Accessor:** `fullName()` — returns trimmed `"{first_name} {last_name}"`
- **Relationships:** `plans()` (HasMany), `tasks()` (HasMany), `categories()` (HasMany)
- **Soft deletes:** Yes (only entity with this)

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
| `plan_id` | `bigint unsigned` | Foreign key → `plans.id`, cascade on delete, nullable |
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
- Deleting a Plan cascade-deletes all its Tasks.
- Deleting a Category with active tasks is rejected by the database (`restrictOnDelete`).

### Factories

| Factory | Key Details |
|---------|-------------|
| `UserFactory` | Cached `Hash::make('password')`, `UserGender::cases()` for gender |
| `CategoryFactory` | `fake()->unique()->word()` for name, inline `User::factory()` |
| `TaskFactory` | `task_date` ≥ today, `estimated_minutes` 1–1200, `TaskPriority::cases()` for priority, nested User/Plan/Category factories |
| `PlanFactory` | `start_date` ±1 month, `finish_date` closure depends on `start_date` + up to 3 months |

### Seeders

- `DatabaseSeeder` — calls `UserSeeder`
- `UserSeeder` — currently empty (admin user placeholder for after auth/roles)

---

## 3.2 — Architecture Principles & Patterns (Overview)

*This section defines the conventions and patterns we will follow during Phase 4 implementation. These are guiding principles, not concrete code — the actual services, actions, and components will be created during Phase 4 as each use case is implemented.*

### Code Organization

```
app/
├── Actions/          # Single-purpose operations (one class = one action)
├── Enums/            # Backed enums for fixed value sets
├── Exceptions/       # Domain-specific exception classes
├── Http/
│   ├── Controllers/  # Thin controllers (if any)
│   └── Livewire/     # Full-page and nested Livewire components
├── Models/           # Eloquent models
├── Services/         # Business logic grouped by domain concern
└── View/             # View composers or presenters (if needed)
```

### Layer Separation Principle

The system follows a simple layered architecture:

```
Livewire Component (UI state + user interaction)
       ↓
Service / Action (business logic)
       ↓
Model (data access via Eloquent)
```

**Rules:**
- **Livewire components** should be thin — handle UI state, validation, and delegate to Services/Actions. No raw Eloquent queries in components.
- **Services** own business logic for a domain area (e.g., Task, Category, Plan, Workload). Can group related operations.
- **Actions** are single-purpose classes for operations with side effects (e.g., deleting a plan also recalculates workload). Use when an operation does more than one thing.
- **Models** handle data access only — no business logic beyond scopes and accessors.

### Pattern Decisions

| Pattern | Decision | When to Apply |
|---------|----------|---------------|
| **Service Layer** | ✅ Use | Group related business logic. One service per domain area (e.g., `TaskService`, `WorkloadService`). |
| **Action Classes** | ✅ Use | Extract any operation that triggers side effects (e.g., recalculating workload after deleting a task). |
| **Backed Enums** | ✅ Use | Already in place for fixed value sets. Extend as new value sets appear. |
| **Repository** | ❌ Defer | Start with Eloquent scopes. Only extract repositories if query logic becomes unmanageable. |
| **Form Request** | ❌ Defer | Livewire components handle validation natively via `rules()` and `$this->validate()`. |
| **View Composer** | ❌ Defer | Format data directly in Livewire component properties. |

### Naming Conventions

| Layer | Naming | Example |
|-------|--------|---------|
| Service | `{Domain}Service` | `TaskService`, `WorkloadService` |
| Action | `{Verb}{Noun}Action` | `CreateTaskAction`, `ToggleTaskDoneAction` |
| Livewire (full-page) | `{View}Page` | `TodayTasksPage`, `PlanListPage` |
| Livewire (nested) | `{ComponentName}` | `TaskCard`, `FilterBar` |
| Exception | `{Description}Exception` | `CategoryHasTasksException` |

### Service Boundaries (Planned)

*These service areas will be fleshed out during Phase 4. Listed here as a map of responsibilities.*

| Domain Area | Responsibilities |
|-------------|-----------------|
| **Task** | Task CRUD, toggle done, workload recalculation trigger |
| **Category** | Category CRUD, delete protection check |
| **Plan** | Plan CRUD, progress calculation |
| **Workload** | Daily total calculation, color/message mapping |
| **Overdue** | Query tasks past their date and not done |
| **Upcoming** | Query tasks within their notification window |
| **Report** | Aggregate performance data over a date range |

### When to Create an Action vs. a Service Method

- **Service method** — when the operation is straightforward and belongs clearly to one domain (e.g., `TaskService::update()`).
- **Action class** — when the operation has significant side effects or crosses domain boundaries (e.g., `DeletePlanAction` also cascade-deletes tasks and recalculates workload).

---

## 3.3 — Error Handling & Logging Principles (Overview)

*This section defines the conventions for handling errors and logging. Actual exception classes and logger calls will be added during Phase 4 implementation.*

### Error Types

| Type | How It's Raised | Example |
|------|-----------------|---------|
| **Validation error** | Livewire `$this->validate()` | Missing title, past date |
| **Authorization failure** | `$this->authorize()` or `Gate` | Editing another user's task |
| **Domain exception** | Custom exception class | Deleting a category that still has tasks |
| **System error** | PHP/Laravel exception | Database connection lost |

### Domain Exception Convention

Create a custom exception class when a business rule is violated and the user needs a specific message:

| Exception | When Thrown |
|-----------|-------------|
| `CategoryHasTasksException` | Attempting to delete a category that still has tasks |
| `TaskDateLockedException` | Attempting to change the date of an existing task |

*Add more as new rules emerge during Phase 4.*

### Handling Strategy

| Layer | How to Handle |
|-------|---------------|
| **Livewire Component** | Catch domain exceptions → show user-friendly message via `session()->flash()` or `$this->addError()`. For validation → automatic per-field messages. |
| **Service / Action** | Throw domain exceptions for rule violations. Don't catch Laravel system exceptions — let them propagate to the global handler. |
| **Global Handler** | `App\Exceptions\Handler` — log unexpected errors with full stack trace, return a generic "Something went wrong" message. |

### Logging Conventions

| Level | When to Log |
|-------|-------------|
| `info` | Successful operations (task created, plan deleted, etc.) |
| `warning` | Failed attempts (validation failures, unauthorized access) |
| `error` | Unexpected system errors, database failures, service exceptions |

- Use `Log::info()`, `Log::warning()`, `Log::error()` directly in Services/Actions.
- Don't log in Livewire components — delegate to the Service/Action layer.
- Local development uses stack logging; production can use daily files.

### Error Visibility for Users

| Error Type | User Sees |
|------------|-----------|
| Validation error | Per-field message (Livewire handles this) |
| Authorization failure | "You are not authorized to perform this action." |
| Domain exception (e.g., category has tasks) | User-friendly explanation: "This category still has tasks. Reassign or delete them first." |
| Unexpected system error | "Something went wrong. Please try again." |

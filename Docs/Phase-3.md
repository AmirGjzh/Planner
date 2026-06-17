# Phase 3 – Detailed Design

Phase 3 covers five areas of detailed design:

1. **Logical Data Model (Completed)** — Tables, columns, keys, constraints, enums, factories, seeders
2. **Livewire Component & Interaction Layer Design (Planned)** — Livewire components, routes, page layouts
3. **Domain Layer & Service Design (Planned)** — Services, Actions, Form Requests
4. **Design Pattern Planning (Planned)** — Patterns to apply, where and why
5. **Error Handling & Logging Strategy (Planned)** — Error structures, log levels, visibility policy

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

## 3.2 — Livewire Component & Interaction Layer Design (Planned)

*This section will be completed during implementation.*

### Page / Component Structure

The UI is built with Laravel Blade layouts and Livewire components (no API layer). Each major view has a full-page Livewire component.

#### Layout Hierarchy

```
layouts/
├── app.blade.php          # Main authenticated layout (sidebar + navbar)
└── guest.blade.php        # Guest layout (login/register pages)
```

#### Full-Page Livewire Components (Routes)

| Route | Component | Description |
|-------|-----------|-------------|
| `/` or `/today` | `App\Http\Livewire\TodayTasks` | Today's task list with workload indicator |
| `/week` | `App\Http\Livewire\WeeklyView` | Weekly grid view |
| `/calendar` | `App\Http\Livewire\CalendarView` | Monthly calendar with clickable days |
| `/tasks/{task}/edit` | `App\Http\Livewire\TaskEdit` | Edit task form (date locked) |
| `/categories` | `App\Http\Livewire\CategoryManager` | Category CRUD page |
| `/plans` | `App\Http\Livewire\PlanList` | Plan list with progress |
| `/plans/{plan}` | `App\Http\Livewire\PlanShow` | Plan detail with its tasks |
| `/overdue` | `App\Http\Livewire\OverdueTasks` | Overdue tasks list |
| `/upcoming` | `App\Http\Livewire\UpcomingTasks` | Upcoming tasks list |
| `/reports` | `App\Http\Livewire\PerformanceReport` | Report with date range selector |
| `/profile` | `App\Http\Livewire\ProfileEditor` | Edit profile info |
| `/login` | — | Blade view (Laravel Breeze/Jetstream) |
| `/register` | — | Blade view |

#### Nested / Inline Livewire Components

| Component | Used In | Purpose |
|-----------|---------|---------|
| `task-card` | All task lists | Render a single task row with toggle, edit, delete buttons |
| `workload-indicator` | Daily/Weekly/Calendar views | Display color + message for a day |
| `category-selector` | Task forms | Dropdown to pick category |
| `plan-selector` | Task forms | Dropdown to pick plan (or "No Plan") |
| `filter-bar` | All task lists | Category / Plan / Status / Priority / Date range filters |
| `confirm-dialog` | Delete actions | Confirmation modal |

### Route Design

All routes are grouped under `auth` middleware. Guest routes (login, register) use `guest` middleware.

```
// Guest routes
Route::get('/login')->uses([...])->name('login');
Route::get('/register')->uses([...])->name('register');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/', TodayTasks::class)->name('today');
    Route::get('/week', WeeklyView::class)->name('week');
    Route::get('/calendar', CalendarView::class)->name('calendar');
    Route::get('/categories', CategoryManager::class)->name('categories');
    Route::get('/plans', PlanList::class)->name('plans');
    Route::get('/plans/{plan}', PlanShow::class)->name('plans.show');
    Route::get('/overdue', OverdueTasks::class)->name('overdue');
    Route::get('/upcoming', UpcomingTasks::class)->name('upcoming');
    Route::get('/reports', PerformanceReport::class)->name('reports');
    Route::get('/profile', ProfileEditor::class)->name('profile');
});
```

### Input Validation Contracts

Each form has a corresponding Form Request or Livewire validation rules:

| Form | Rules |
|------|-------|
| Create Category | `name` required, string, max:255, unique per user |
| Create Task | `title` required, `category_id` required + exists, `estimated_minutes` required + integer + min:1, `task_date` required + date, `priority` optional + in:low,medium,high |
| Create Plan | `name` required, `start_date` required + date, `finish_date` required + date + after_or_equal:start_date |
| Profile | `first_name`, `last_name` optional strings, `birth_date` optional date, `country` optional string, `gender` optional + in:male,female |

---

## 3.3 — Domain Layer & Service Design (Planned)

*This section will be completed during implementation.*

### Service Layer

Services contain business logic extracted from controllers/Livewire components. Each service class is responsible for one domain area.

| Service | Methods | Responsibility |
|---------|---------|----------------|
| `TaskService` | `create()`, `update()`, `delete()`, `toggleDone()` | Task CRUD + workload recalculation trigger |
| `CategoryService` | `create()`, `update()`, `delete()` | Category CRUD with delete protection check |
| `PlanService` | `create()`, `update()`, `delete()`, `progress()` | Plan CRUD + progress calculation |
| `WorkloadService` | `calculateForDay()`, `calculateForRange()`, `color()`, `message()` | Daily workload math + color/message mapping |
| `OverdueService` | `getOverdueTasks()` | Query tasks past due and not done |
| `UpcomingService` | `getUpcomingTasks()` | Query tasks within notification window |
| `ReportService` | `generate()` | Aggregate data for performance reports |

### Action Classes

Single-purpose action classes for operations that deserve their own class:

| Action | Input | Output | Side Effects |
|--------|-------|--------|--------------|
| `CreateTaskAction` | Validated data, User | Task | Recalculates workload |
| `DeleteTaskAction` | Task | void | Recalculates workload |
| `ToggleTaskDoneAction` | Task | bool (new status) | Recalculates overdue/upcoming |
| `DeletePlanAction` | Plan | void | Cascade deletes tasks, recalculates workload |
| `AssignTaskToPlanAction` | Task, Plan|null | void | — |
| `DeleteCategoryAction` | Category | void | Throws if tasks exist |

### Form Requests

| Form Request | Rules |
|--------------|-------|
| `StoreTaskRequest` | Validation for task creation |
| `UpdateTaskRequest` | Validation for task editing (date locked) |
| `StoreCategoryRequest` | Validation for category creation |
| `StorePlanRequest` | Validation for plan creation |
| `UpdatePlanRequest` | Validation for plan editing |
| `ProfileUpdateRequest` | Validation for profile editing |

### Interaction Flow

```
Livewire Component
    ↓ calls
Service / Action class
    ↓ uses
Model (Eloquent)
    ↓ persist
Database
    ↓ return
Result to Livewire Component
    ↓ update
UI via Livewire re-render
```

---

## 3.4 — Design Pattern Planning (Planned)

*This section will be completed during implementation.*

### Patterns to Apply

| Pattern | Where to Use | Rationale |
|---------|-------------|-----------|
| **Service Layer** | `App\Services\*` | Encapsulates business logic outside controllers/components. Each service owns one domain concern. |
| **Action Class** | `App\Actions\*` | Single-purpose classes for operations with side effects (e.g., `CreateTaskAction` also recalculates workload). Keeps Livewire components thin. |
| **Form Request** | `App\Http\Requests\*` | Centralized validation rules with authorization gates. Keeps validation out of components. |
| **Repository (Consider)** | `App\Repositories\*` | If query logic becomes complex (e.g., filtered/sorted task lists with pagination). May be overkill for MVP — start with scopes on the model. |
| **Enum (Backed)** | `App\Enums\*` | Already using for `TaskPriority` and `UserGender`. Provides type safety and a single source of truth for fixed value sets. |
| **View Composer / Presenter** | `App\View\Composers\*` | If view data formatting becomes repetitive. For MVP, format data directly in Livewire component properties. |
| **Factory** | `Database\Factories\*` | Already implemented for all 4 models. Used for seeding and testing. |

### Decision Log

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Service vs direct Eloquent in components | Services | Keeps Livewire components testable and focused on UI state |
| Actions vs Services for operations | Both | Services for grouped operations, Actions for single-purpose operations with side effects |
| Repository pattern | Defer to MVP+1 | Start with Eloquent scopes and query builders; extract Repository only if needed |
| Form Request vs inline validation | Form Request | Keeps Livewire component `rules()` method clean; reusable if validation is needed elsewhere |

---

## 3.5 — Error Handling & Logging Strategy (Planned)

*This section will be completed during implementation.*

### Error Design

#### Domain-Specific Exceptions

Custom exceptions in `App\Exceptions\`:

| Exception | When Thrown |
|-----------|-------------|
| `CategoryHasTasksException` | Attempting to delete a category that still has tasks |
| `TaskDateLockedException` | Attempting to change the date of an existing task |
| `PlanNotEmptyException` | (Optional) Attempting operations on a plan with active constraints |

#### Exception Handling Strategy

| Layer | How Errors Are Handled |
|-------|----------------------|
| **Livewire Component** | Catch exceptions, set `$this->addError()` for field-level messages, or `session()->flash('error')` for general messages |
| **Service / Action** | Throw domain exceptions or return Result objects |
| **Form Request** | Automatic validation error response (Livewire catches and displays per-field) |
| **Global Handler** | `App\Exceptions\Handler` — log unexpected errors, return generic user-friendly message |

### Logging Strategy

#### Log Levels

| Level | When to Use |
|-------|-------------|
| `info` | Task created, updated, deleted; plan operations; user login/logout |
| `warning` | Failed validation attempts, unauthorized access attempts |
| `error` | Unexpected exceptions, database failures, service errors |
| `critical` | (Future) Payment failures, data corruption events |

#### Log Channels

- **Local development:** `stack` (single log file)
- **Production:** Daily log files with separate channels for specific concerns if needed

### Error Visibility Policy

| Error Type | User Sees | Logged? |
|------------|-----------|---------|
| Validation error | Per-field message (Livewire) | No |
| Authorization failure | "You are not authorized to perform this action" | `warning` |
| Domain exception (e.g., category has tasks) | User-friendly message: "This category still has tasks. Reassign or delete them first." | `info` |
| Unexpected system error | "Something went wrong. Please try again." | `error` with full stack trace |
| Database connection failure | "Service temporarily unavailable" | `error` |

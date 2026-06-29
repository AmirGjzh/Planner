# Session Context — Planner

## Project Identity

- **Product:** Planner — personal daily/weekly task management app (replacing Telegram-based tracking)
- **Goal:** Users create tasks with categories, assign them to calendar days, organize into plans, track workload, view completion reports
- **MVP scope:** Single-user per account — no team/sharing features
- **Tech Stack:** Laravel 13, Livewire v4.3, Alpine.js v3, Tailwind CSS v4, Pest v4.7, PHP 8.4, MySQL/MariaDB

## Locations

| What | Path |
|------|------|
| App root | `C:\Users\AmirMohammad\Programming\Laravel\Planner` |
| Documentation | `C:\Users\AmirMohammad\Programming\Laravel\Docs` |
| PHP binary | `C:\Laravel\tools\php\8.4\php.exe` |
| JS entry | `Planner/resources/js/app.js` |

## File Map

### Models (4)
- `app/Models/User.php` — SoftDeletes, `user_name` (unique), profile fields (first_name, last_name, birth_date, country, gender), hasMany(plans/tasks/categories)
- `app/Models/Category.php` — name, belongsTo(user), hasMany(tasks)
- `app/Models/Plan.php` — name, description, start_date, finish_date, done, belongsTo(user), hasMany(tasks)
- `app/Models/Task.php` — title, description, task_date, estimated_minutes, priority, done, day_before_alarm, belongsTo(user/category/plan)

### Actions (14)
- `app/Actions/Auth/` — LoginUserAction, RegisterUserAction, LogoutUserAction, DeleteAccountAction
- `app/Actions/Profile/` — UpdateProfileAction
- `app/Actions/Category/` — CreateCategoryAction, EditCategoryAction, DeleteCategoryAction
- `app/Actions/Plan/` — CreatePlanAction, EditPlanAction, DeletePlanAction
- `app/Actions/Task/` — CreateTaskAction, EditTaskAction, DeleteTaskAction

### Enums (16)
- **Auth:** LoginResult (Success/Fail/RateLimited), RegisterResult (Success/UsernameTaken/EmailTaken/RateLimited), DeleteAccountResult (Success/WrongPassword/RateLimited)
- **Profile:** UpdateProfileResult (Success/UsernameTaken/RateLimited), UserGender (Male/Female/Other)
- **Category:** CreateCategoryResult, EditCategoryResult, DeleteCategoryResult
- **Plan:** CreatePlanResult, EditPlanResult, DeletePlanResult
- **Task:** CreateTaskResult (Created/RateLimited/InvalidCategory/InvalidPlan), EditTaskResult (Updated/RateLimited/InvalidCategory/InvalidPlan), DeleteTaskResult (Deleted), TaskPriority (Low/Medium/High)
- **Other:** DateRangePreset (Custom/Today/ThisWeek/ThisMonth)

### Policies (3)
- `app/Policies/CategoryPolicy.php` — create: true, update/delete: ownership
- `app/Policies/PlanPolicy.php` — create: true, update/delete: ownership
- `app/Policies/TaskPolicy.php` — create: true, update/delete: ownership

### Pages (full-page Livewire components, co-located)
- `pages/⚡home/` — guest landing page
- `pages/⚡dashboard/` — authenticated dashboard (placeholder)
- `pages/⚡profile/` — profile view + edit modal + delete account
- `pages/⚡category-page/` — CRUD with inline edit (Alpine `x-model`, no `wire:model`)
- `pages/⚡plan-page/` — CRUD with edit modal
- `pages/⚡task-page/` — CRUD with edit modal
- `pages/auth/⚡login/` — login form
- `pages/auth/⚡register/` — register form

### Routes (`routes/web.php`)
- **Guest:** `/` (home), `/login`, `/register`
- **Auth:** `/dashboard`, `/profile`, `/category-page`, `/plan-page`, `/task-page`, `POST /logout`

### UI Components (`resources/views/components/ui/`)
Extensive library: button, input, select, checkbox, date-picker, modal, dropdown, card, badge, heading, icon, separator, field, label, error, text, link, kbd, avatar, brand, alert, badge, layout (header+sidebar variants), navbar, navlist, popup, textarea

### Layout Partials (`resources/views/components/layouts/partials/`)
- `⚡user-dropdown/` — Livewire component (logout via Alpine fetch + Livewire.navigate)
- `⚡mobile-menu/` — Livewire component
- `⚡nav-links/` — Livewire component
- `⚡guest-action/` — Livewire component (login/register links)

### Layouts
- `resources/views/layouts/app.blade.php` — authenticated layout
- `resources/views/layouts/auth.blade.php` — guest layout (login/register)

### Migrations
- `create_users_table.php` — SoftDeletes, unique user_name + email
- `create_categories_table.php` — unique(user_id, name), FK user_id cascadeOnDelete
- `create_plans_table.php` — FK user_id cascadeOnDelete
- `create_tasks_table.php` — FK user_id cascadeOnDelete, FK category_id restrictOnDelete, FK plan_id restrictOnDelete (nullable)

### Synthesizers
- `app/Livewire/Synthesizers/DateRangeSynthesizer.php` — custom Livewire property synth for CarbonPeriod-backed DateRange

## Architecture Patterns

### Action Layer (all business logic)
- Single-purpose classes in `app/Actions/{Domain}/` with one `execute()` method
- All DB queries live in Actions — never in Livewire components or Blade
- Resolved via `app(ActionClass::class)` in tests and `\App::make()` in components
- Pattern: relationship-scoped findOrFail → Policy check (`abort_unless($user->can(...), 403)`) → rate limit check (before existence queries) → business logic → log → return result enum
- Ownership enforced by `$user->relationship()->findOrFail($id)` — throws `ModelNotFoundException` for other-user IDs
- Authorization in Action layer (not component) makes it frontend-agnostic

### Result Enums
- One enum per action group: `Create{Name}Result`, `Edit{Name}Result`, `Delete{Name}Result`
- TitleCase keys: `Created`, `RateLimited`, `InvalidCategory`, etc.
- Returned from `execute()` — component checks result and maps to user-facing errors

### Rate Limiting
- Key format: `'{action-name}:{user.id}|{request.ip}'`
- **5 attempts per minute** per user+IP
- Checked **before** existence/ownership DB queries (prevents unnecessary DB hits while rate-limited)
- `RateLimiter::hit($key, 60)` on every non-rate-limited attempt
- Separate keys per action: `create-task:`, `edit-task:`, `create-plan:`, `edit-plan:`, `create-category:`, `edit-category:`
- Delete is NOT rate-limited (infrequent, destructive)

### Validation
- **Format/bounds:** Inline `$this->validate([...])` in Livewire component methods
- **Existence/ownership:** In Actions after rate limiter check
- Component validates: required, string max, integer min/max, date format, priority in:low,medium,high, etc.
- Action validates: category/plan belongs to user, name uniqueness scoped to user, etc.

### Livewire Component Pattern
- Full-page components in `resources/views/pages/⚡{name}/`
- `#[Locked] public int $userId` — set in `mount()`, prevents client tampering
- `#[Computed]` for lists with eager loading: `#[Computed] public function tasks()` with `->with('category', 'plan')`
- Cache busting: `unset($this->{listProperty})` after any mutation
- `mount()` initializes defaults (e.g., `$this->task_date = now()->format('Y-m-d')`)
- Form reset: clear input fields and restore defaults after successful submission

### Edit Modal Pattern (Plan & Task pages)
1. Button → Alpine dispatches `$dispatch('open-modal', { id: 'edit-{name}-modal' })` — modal opens INSTANTLY with empty fields
2. `$wire.startEditing($id)` called async — Livewire queries model, populates edit-scoped properties
3. User edits → Save → `update{Name}()` validates, calls Action, handles errors
4. On success: `$this->dispatch('close-modal', id: ...)` from server → modal closes → `cancelEditing()` resets
5. Cancel: `$data.close(); $wire.cancelEditing()` — Alpine closes first, Livewire resets after (prevents flicker)
6. `wire:target` on Save button shows loading state

### Category Page Pattern (no wire:model)
- Uses Alpine `x-model` + `$wire.call('addCategory')` — no `wire:model` race conditions
- Inline edit: `editingId` tracked in Alpine (`x-data="{ editingId: null }"`), at most one open simultaneously

### Cascade-on-Delete Rules
- `user_id`: `cascadeOnDelete` (deleting user removes all their data)
- `plan_id` on tasks: `restrictOnDelete` (plan with tasks cannot be deleted — Action checks count first, DB is final guard)
- `category_id` on tasks: `restrictOnDelete` (category with tasks cannot be deleted)
- Plans/categories remain after owner leaves; admin handles cleanup
- Deleting a user still cascades

### Livewire + Alpine Integration
- `config/livewire.php`: `'inject_assets' => false` — prevents Livewire from injecting separate Alpine
- `@livewireScriptConfig` before `@vite` in `<head>` — REQUIRED when `inject_assets` is false
- `Livewire.start()` called manually in `app.js` after all imports and `Alpine.data()` registrations
- `alpine:init` event listener wraps all `Alpine.data()` calls — guarantees registrations during `Alpine.start()`
- `@livewireStyles` in both layouts

### DateRange Synthesizer
- Custom synthesizer handles CarbonPeriod-extended DateRange objects
- Setting in tests: `->set('range', ['start' => '2026-01-01', 'end' => '2026-01-31'])` — array format that `hydrateFromType()` handles
- Setting sub-properties: `->set('range.start', '2026-06-15')` via `DateRangeSynthesizer::set()`
- Range validated with `date_format:Y-m-d` and `after:range.start`

## Phase Documents

| Doc | Content |
|-----|---------|
| Phase-0 | Vision & MVP scope, user roles, 23 usecase overview |
| Phase-1 | User stories for all 23 usecases, functional/non-functional requirements |
| Phase-2 | Conceptual domain design: entities, relationships, detailed usecase flows, acceptance criteria |
| Phase-3 | Logical data model (4 tables, migrations, FKs, enums, factories, seeders), architecture principles, error/logging conventions |
| Phase-4 | Iterative implementation docs per UC including implementation files, testing files, security notes, acceptance results (609 lines) |
| Livewire.md | Livewire v4 performance tricks, caching strategies, Alpine hybrid patterns |
| Laravel.md | Comprehensive Laravel best practices bible |

## Completed Use Cases (12 of 23)

| UC | Feature | Tests (Total) |
|----|---------|:------------:|
| 01 | Login | Action + Livewire + Access |
| 02 | Register | Action + Livewire + Access |
| 03 | View & Edit Profile | 5 |
| 04 | Logout | 2 |
| 05 | Delete Account | 5 |
| 06 | Create Task | 28 |
| 07 | Edit Task | 19 |
| 08 | Delete Task | 6 |
| 10 | Manage Categories | 33 |
| 19 | Create Plan | 26 |
| 20 | Edit Plan | 27 |
| 21 | Delete Plan | 24 |

### Not Yet Started (11)
- UC-09: Calendar View (daily/weekly/monthly)
- UC-11: Mark Task Done/Not Done
- UC-12: View Day Details
- UC-13: Daily Workload Calculation
- UC-14: Color-Coded Day Indicators
- UC-15: Performance Reports
- UC-16: Overdue Tasks View
- UC-17: Auto Not-Done Overnight
- UC-18: Plan Progress
- UC-22: View Plan Tasks
- UC-23: Plan Progress Tracking

## Testing Structure

### Test Locations
- `tests/Feature/Actions/{Domain}/{ActionName}Test.php` — Action unit tests with Pest
- `resources/views/pages/⚡{page}/{page}.test.php` — Co-located Livewire component tests
- `tests/Feature/Auth/{Page}AccessTest.php` — Route/middleware access tests

### Key Conventions
- Pest with `LazilyRefreshDatabase`
- Livewire components registered as `'pages::{name}'` (e.g., `'pages::plan-page'`)
- Clear rate limiters before testing: `RateLimiter::clear('{key}:'.$user->id.'|127.0.0.1')`
- Travel to reset rate limit windows: `$this->travel(61)->seconds()`
- DB assertions: `expect($model->fresh())->{field}->toBe($value)`
- Validation errors: `->assertHasErrors(['field'])`
- Property checks: `->assertSet('field', $value)`
- Ownership cross-user tests: expect `ModelNotFoundException` from relationship-scoped `findOrFail`
- Logging assertions: `Log::spy()` + `Mockery::on()`

### phpunit.xml Test Suites
```
Feature    → tests/Feature/
Components → resources/views/**/*.test.php
Unit       → tests/Unit/
```

## Key Commands

```bash
# Use full PHP path (not in PATH)
C:\Laravel\tools\php\8.4\php.exe artisan serve
C:\Laravel\tools\php\8.4\php.exe artisan test --compact
C:\Laravel\tools\php\8.4\php.exe artisan test --compact --filter={name}
C:\Laravel\tools\php\8.4\php.exe artisan test --testsuite=Components
C:\Laravel\tools\php\8.4\php.exe artisan test --parallel
C:\Laravel\tools\php\8.4\php.exe artisan make:test --pest {name}

# Code formatting
vendor/bin/pint --format agent

# Route inspection
C:\Laravel\tools\php\8.4\php.exe artisan route:list

# Build frontend
npm run build
```

## Critical Gotchas

- **PHP not in PATH** — always use `C:\Laravel\tools\php\8.4\php.exe`, never just `php`
- **`@livewireScriptConfig` REQUIRED** before `@vite` in `<head>` when `inject_assets` is false
- **`Livewire.start()` must be called manually** in `app.js` when `inject_assets` is false
- **`alpine:init` wrapper required** for all `Alpine.data()` registrations in separate ESM files
- **DateRange in tests uses array format**: `['start' => '...', 'end' => '...']`, not `new DateRange()`
- **Rate limit key includes IP** — in tests it's `127.0.0.1`; clear with `->clear('{key}:'.$user->id.'|127.0.0.1')`
- **`DeleteTaskResult` has only `Deleted`** — tasks are leaf entities, no dependent records
- **`CreateTaskResult`/`EditTaskResult` include `InvalidCategory`/`InvalidPlan`** — not needed in Plan/Category CRUD
- **Edit modal opens with empty fields** (UX-first), then `$wire.startEditing()` populates async — intentional
- **`$this->dispatch('close-modal')` from server** closes modal on success (not from Alpine handler)
- **PINT**: run `vendor/bin/pint --format agent` (NOT `php artisan pint`)
- **Category form** uses Alpine `x-model` + `$wire.call()` (NOT `wire:model`)

## Environment & Configuration

- `config/livewire.php` — `inject_assets => false`
- `opencode.json` — Laravel Boost MCP enabled via `php artisan boost:mcp`
- `boost.json` — Skills enabled: laravel-best-practices, livewire-development, pest-testing, tailwindcss-development; agents: opencode
- `AGENTS.md` — Laravel Boost guidelines (must read; governs all work)
- `phpunit.xml` — 3 test suites, parallel testing (12 processes)
- `.env` — database credentials, app config

## Laravel Boost MCP Tools
- `database-query` — read-only DB queries
- `database-schema` — inspect table structure
- `search-docs` — search Laravel/Livewire docs by version
- `get-absolute-url` — resolve project URLs
- `browser-logs` — read browser errors
- Always `search-docs` before making code changes
- Use multiple broad queries for doc search: `['rate limiting', 'routing']`

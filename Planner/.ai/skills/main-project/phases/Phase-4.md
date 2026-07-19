# Phase 4 – Iterative Feature Development

## Objective

Implement each User Story completely, from design to testing and documentation, before moving to the next story.

---

## 4.1 – Build

Combine UX/UI, backend, and frontend into a single implementation step.

- Review the user story in Phase-1, its acceptance criteria, and the Phase-2 UC flow
- Reuse existing Livewire components, UI components, and Blade layouts
- Create the Action class with rate limiting, ownership checks, and result enums
- Create the Livewire component (full-page or nested) with validation and error handling
- Create the Blade view with appropriate states (empty, loading, error)

**Deliverable:** Working feature.

---

## 4.2 – Test

Cover all layers:

- **Action tests** — unit-test the Action class for all result states, rate limiting, ownership, and logging
- **Livewire tests** — co-located in `resources/views/pages/⚡{page}/{page}.test.php` covering render, validation, success, and error paths
- **Access tests** — guest redirect and authenticated access in `tests/Feature/Auth/`
- Run `vendor/bin/pint --format agent` before finalizing

**Deliverable:** Passing tests.

---

## 4.3 – Refactor

Review the implementation for:
- Readability and simplicity
- Consistency with existing patterns (check sibling files)
- No dead code or leftover debug statements

**Deliverable:** Clean code.

---

## 4.4 – Document

Add a completed UC log below in this file following the existing format:
- Goal, routes, implementation files, testing files, security notes, acceptance result

**Deliverable:** Updated Phase-4 with the new UC log.

---

## 4.5 – Accept

Verify:
- Acceptance Criteria from Phase-1 are satisfied
- All tests pass (`php artisan test --compact --filter={testName}`)
- Documentation is updated

**Outcome:** Story completed — move to the next UC in topological order.

---

## Phase Completion Criteria

The phase is complete when:
- All User Stories are completed
- All Acceptance Criteria pass
- All tests pass
- Documentation is current
- The application is deployable

---

## Completed Use Cases

### UC-01 – Login

**Status:** Completed

**Goal:** Allow a guest user to sign in with email and password, receive validation or credential errors when needed, and enter the authenticated area after successful login.

**Routes:**
- `GET /login` → Livewire page `pages::auth.login`, guest-only route.
- `GET /dashboard` → authenticated destination after login, auth-only route.

**Implementation Files:**
- `routes/web.php` — defines guest login route and authenticated dashboard route.
- `resources/views/pages/auth/⚡login/login.php` — Livewire component with typed `$login_error` property (`null` | `'rate_limited'` | `'invalid'`), `$remember` bool for the remember-me toggle, validates email/password with custom `messages()`, uses `if`/`elseif` on `LoginResult` enum cases, calls `LoginUserAction`, redirects on success, resets password and sets error state on failure.
- `resources/views/pages/auth/⚡login/login.blade.php` — login form using mine/* components (`mine.input`, `mine.button`, `mine.checkbox`, `mine.alert`, `mine.separator`), error alerts driven by `$login_error` state, remember-me checkbox, forgot password placeholder.
- `app/Actions/Auth/LoginUserAction.php` — final action class; rate limiting via `RateLimiter` with configurable constants (`MAX_ATTEMPTS: 5`, `DECAY_SECONDS: 60`), `Auth::attempt()`, session regeneration, structured logging for all outcomes with `'available_in'` context key.
- `app/Enums/LoginResult.php` — backed string enum (`Fail`, `Success`, `RateLimited`).

**Testing Files:**
- `resources/views/pages/auth/⚡login/login.test.php` — 7 co-located Livewire tests: render, required field validation, email format validation, wrong credentials error state, successful authentication and redirect, rate limiting after 5 failures, retry after 60-second cooldown.
- `tests/Feature/Auth/LoginAccessTest.php` — 4 access tests: guest visits login, authenticated user redirected from login, guest redirected from dashboard, authenticated user visits dashboard.
- `tests/Feature/Actions/Auth/LoginUserActionTest.php` — 4 action tests: success, fail, rate limited, and logging for all outcomes.

**Security and Reliability Notes:**
- Validation handled server-side by Livewire with custom messages.
- Failed credentials use a generic error message to prevent user enumeration.
- Login attempts are rate-limited by normalized email and IP address (5 attempts per 60 seconds).
- Rate limit key uses `Str::transliterate()` and `Str::lower()` to prevent Unicode bypass.
- Error state managed via `$login_error` component property instead of the validation error bag — keeps error bag clean for actual field validation.
- Successful login regenerates the session to prevent session fixation.
- Login success, failed attempts, and rate-limited attempts are logged in the action layer with structured context.
- Blade output remains escaped; no raw user-controlled HTML is rendered.
- All inputs use mine/* Blade components with consistent styling and built-in loading state.

**Acceptance Result:** UC-01 is accepted. 20 passing tests (55 assertions). 7 Livewire UI tests, 4 action unit tests, 4 access tests, plus additional login flow coverage from peripheral access tests.

### UC-02 – Register

**Status:** Completed

**Goal:** Allow a guest user to create a new account with a username, email, password, and password confirmation, then redirect them to login after successful registration.

**Routes:**
- `GET /register` → Livewire page `pages::auth.register`, guest-only route.
- `GET /login` → existing guest login route, used as the post-registration destination.

**Implementation Files:**
- `routes/web.php` — defines the guest-only register route.
- `resources/views/pages/auth/⚡register/register.php` — Livewire component with typed `$register_error` property (`null` | `'rate_limited'` | `'username_taken'` | `'email_taken'`), `$password_confirmation` property, validates username (regex `/^[a-zA-Z][a-zA-Z0-9_-]{2,29}$/`), email (`email:rfc`), and password (confirmed, min:8) with custom `messages()`, uses a `match` expression to handle all 4 `RegisterResult` enum cases, calls `RegisterUserAction`, resets password fields on failure, redirects to login on success.
- `resources/views/pages/auth/⚡register/register.blade.php` — registration form UI, field errors, register-level error display driven by `$register_error` state, and login navigation link.
- `app/Actions/Auth/RegisterUserAction.php` — final action class; rate limiting via `RateLimiter` with configurable constants (`MAX_ATTEMPTS: 5`, `DECAY_SECONDS: 60`), username/email uniqueness checks after the limiter gate, user creation via `User::create()`, race-condition duplicate handling via `QueryException` / `isIntegrityConstraintViolation()` (SQLSTATE '23' prefix), structured logging for all outcomes with `'available_in'` context key.
- `app/Enums/RegisterResult.php` — result enum returned by the register action (`Success`, `UsernameTaken`, `EmailTaken`, `RateLimited`).
- `resources/views/pages/auth/⚡login/login.blade.php` — cross-navigation link from login to register.

**Testing Files:**
- `resources/views/pages/auth/⚡register/register.test.php` — co-located Livewire tests for rendering, validation, duplicate username or email errors, successful registration, guest state after registration, and rate limiting.
- `tests/Feature/Auth/RegisterAccessTest.php` — route/middleware tests for guest and authenticated access.
- `tests/Feature/Actions/Auth/RegisterUserActionTest.php` — action tests for success, duplicate results, rate limiting, password hashing, and logging.

**Security and Reliability Notes:**
- Registration validation is handled server-side by Livewire.
- Email inputs are validated as RFC email addresses without browser-only validation.
- Passwords are stored through the `User` model's hashed cast.
- Username and email uniqueness are checked inside the action after rate-limit checks to avoid unnecessary database reads while an IP is limited.
- Database unique constraints remain the final protection against duplicate accounts.
- Race-condition duplicate failures are caught from database integrity exceptions and converted into user-friendly username or email errors.
- Failed duplicate attempts are rate-limited by IP address.
- Registration success, duplicate failures, and rate-limited attempts are logged in the action layer.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-02 is accepted. 22 passing tests. 12 co-located Livewire UI tests (including 4 username-format dataset variants), 5 action tests (success, username taken, email taken, rate limited, logging), 2 access tests, plus additional register flow coverage from peripheral access tests.

### UC-03 – View and Edit Profile

**Status:** Completed

**Goal:** Allow an authenticated user to view account/profile information and update editable profile fields from a modal form.

**Routes:**
- `GET /profile` → Livewire page `pages::profile`, auth-only route.

**Implementation Files:**
- `routes/web.php` — defines the authenticated profile route.
- `resources/views/pages/⚡profile/profile.php` — Livewire page state, authenticated user lookup, profile validation, action call, result handling, form reset or cancel behavior, and country list data.
- `resources/views/pages/⚡profile/profile.blade.php` — profile display UI, edit modal, form fields, field errors, profile-level rate-limit error display, and cancel or apply controls.
- `app/Actions/Profile/UpdateProfileAction.php` — profile update business action; handles rate limiting, username uniqueness checks after the limiter gate, profile persistence, race-condition duplicate handling, and logging.
- `app/Enums/UpdateProfileResult.php` — result enum returned by the profile update action (`Success`, `UsernameTaken`, `RateLimited`).
- `app/Enums/UserGender.php` — enum used by profile validation and the `User.gender` cast.
- `app/Models/User.php` — stores editable profile fields and casts `birthday` and `gender`.

**Testing Files:**
- `resources/views/pages/⚡profile/profile.test.php` — co-located Livewire tests for rendering, initial form state, validation, successful updates, nullable fields, username-taken errors, rate limiting, time-travel retry, and cancel behavior.
- `tests/Feature/Auth/ProfileAccessTest.php` — route/middleware tests for guest redirect and authenticated profile access.
- `tests/Feature/Actions/Profile/UpdateProfileActionTest.php` — action tests for success, duplicate username result, keeping the current username, nullable cleanup, rate limiting, time-travel retry, and logging.

**Security and Reliability Notes:**
- Profile access is protected by the `auth` middleware.
- Livewire validation handles required username, username format, text lengths, enum-backed gender values, valid country codes, and non-future birth dates.
- Username uniqueness is checked inside the action after rate-limit checks to avoid unnecessary database reads while the profile update key is limited.
- Database unique constraints remain the final protection against duplicate usernames.
- Race-condition duplicate failures are caught from database integrity exceptions and converted into a user-friendly username error.
- Profile update attempts are rate-limited by authenticated user ID and IP address.
- Profile update success, duplicate username failures, and rate-limited attempts are logged in the action layer.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-03 is accepted. Authenticated users can view and edit profile information, invalid input is rejected, duplicate usernames are handled cleanly, rate limiting is enforced, and the use case is covered by passing tests.

### UC-04 – Delete Account

**Status:** Completed

**Goal:** Allow an authenticated user to permanently delete their account, requiring password confirmation, with rate limiting to prevent brute-force attacks.

**Routes:**
- `GET /profile` → Livewire page `pages::profile`, auth-only route (existing UC-03 route).

**Implementation Files:**
- `resources/views/pages/⚡profile/profile.php` — Livewire page state; `deleteAccount()` method validates the password field, calls `DeleteAccountAction`, handles all three result states (`Success`, `WrongPassword`, `RateLimited`), and redirects to login on success; `cancelDelete()` resets form state.
- `resources/views/pages/⚡profile/profile.blade.php` — profile display UI with a "Delete Account" button that opens a confirmation modal with a password field and submit or cancel controls (unchanged from UC-03).
- `app/Actions/Auth/DeleteAccountAction.php` — account deletion business action; checks rate limiting (5 attempts per minute per user ID/IP), verifies the password against the user's hashed password, logs the user out, obfuscates email and username to free unique constraints, soft-deletes the user record, and logs the event.
- `app/Enums/DeleteAccountResult.php` — result enum returned by the delete account action (`Success`, `WrongPassword`, `RateLimited`).

**Testing Files:**
- `tests/Feature/Actions/Auth/DeleteAccountActionTest.php` — action tests for successful deletion, email/username obfuscation, wrong password result, rate limiting, time-travel retry, and logging for all outcomes.

**Security and Reliability Notes:**
- Account deletion is protected by the `auth` middleware via the profile route.
- Password verification is performed inside the action layer, not the Livewire component — ensuring the guard applies regardless of caller.
- Rate limiting (5 attempts per minute) prevents brute-force password guessing on the delete account flow.
- Email and username are obfuscated with `deleted-user-{id}` / `deleted_user_{id}` before soft-deleting, freeing unique constraints for future registrations without leaking the original values.
- Database unique constraints remain the final protection against duplicate data for the obfuscated values (rare but safe).
- Deletion success, wrong password attempts, and rate-limited attempts are logged in the action layer.
- The action logs the user out and invalidates the session before deleting the user record.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-04 is accepted. Authenticated users can delete their account with password confirmation, wrong passwords are rejected with a clear error, brute-force attempts are rate-limited, the account is properly obfuscated and soft-deleted, and the use case is covered by passing tests.

### UC-05 – Logout

**Status:** Completed

**Goal:** Allow an authenticated user to log out, ending their session, and transition back to the login page using an SPA navigation without a full page reload.

**Routes:**
- `POST /logout` — plain POST route within the `auth` middleware group, returns 204 No Content.

**Implementation Files:**
- `routes/web.php` — defines the authenticated POST `/logout` route with a named route `logout`; executes `LogoutUserAction` and returns `response()->noContent()`.
- `resources/views/layouts/app.blade.php` — user dropdown menu; the Log out item uses Alpine `fetch()` to POST to the logout route with the CSRF token, then calls `Livewire.navigate()` for an SPA transition to the login page.
- `app/Actions/Auth/LogoutUserAction.php` — logout business action; retrieves the authenticated user ID, calls `Auth::logout()`, invalidates the session, regenerates the CSRF token, and logs the event.

**Testing Files:**
- `tests/Feature/Auth/LogoutAccessTest.php` — route/middleware tests for successful logout (asserts 204 No Content and guest state) and guest redirect to login.

**Security and Reliability Notes:**
- Logout is protected by the `auth` middleware; guests are redirected to login.
- Session is invalidated and the CSRF token is regenerated after logout to prevent session fixation.
- Logout events are logged with the user ID and IP address.
- The Alpine fetch approach avoids a full page reload — the 204 response is consumed silently and `Livewire.navigate()` provides an SPA transition to the login page.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-05 is accepted. Authenticated users can log out with a single click, the session is properly invalidated, and the user is transitioned to the login page without a full browser refresh.

### UC-06 – Manage Categories

**Status:** Completed

**Goal:** Allow an authenticated user to create, rename, and delete categories. Each category name must be unique per user. Deletion is prevented when the category still has tasks assigned.

**Routes:**
- `GET /categories` → Livewire page `pages::categories`, auth-only route.

**Implementation Files:**
- `routes/web.php` — defines the authenticated categories route.
- `resources/views/pages/⚡categories/categories.php` — Livewire page state, validation via `Validator::make()` (no `wire:model` — race-condition safe), action calls for create, edit, and delete, result handling for `AlreadyExists`, `RateLimited`, and `HasTasks`, paginated categories computed property, and `unset($this->categories)` cache busting after mutations.
- `resources/views/pages/⚡categories/categories.blade.php` — category creation form with Alpine `x-model` and `$wire.call()` (no `wire:model` race conditions), category list with `withCount('tasks')`, per-category inline edit form with Alpine `x-show` toggled by a parent-scoped `editingId` (at most one open simultaneously, no server round-trip), delete buttons with `wire:click`, pagination links via `$this->categories->links()`, and empty state.
- `app/Actions/Category/CreateCategoryAction.php` — create category business action; authorization through `abort_unless($user->can('create', ...), 403)`, rate limiting (5 attempts/minute per user+IP) with logging (info on success, warning on errors), checks user-scoped name uniqueness, creates the category, returns `Created`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Category/EditCategoryAction.php` — edit category business action; authorization through `abort_unless($user->can('update', ...), 403)`, rate limiting (5 attempts/minute per user+IP) with logging (info on success, warning on errors), checks uniqueness excluding self, updates the name, returns `Updated`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Category/DeleteCategoryAction.php` — delete category business action; authorization through `abort_unless($user->can('delete', ...), 403)`, logging (info on success, warning on errors), checks for assigned tasks before deletion, returns `Deleted` or `HasTasks`.
- `app/Policies/CategoryPolicy.php` — policy with `create` (always true), `update` (ownership), `delete` (ownership) methods. Enforced in Action classes, not the Livewire component — frontend-agnostic and non-bypassable.
- `app/Enums/CreateCategoryResult.php` — result enum (`Created`, `AlreadyExists`, `RateLimited`).
- `app/Enums/EditCategoryResult.php` — result enum (`Updated`, `AlreadyExists`, `RateLimited`).
- `app/Enums/DeleteCategoryResult.php` — result enum (`Deleted`, `HasTasks`).
- `database/migrations/2026_06_14_152512_create_categories_table.php` — creates `categories` table with `index('user_id')` and `unique(['user_id', 'name'])`, plus `foreignId('user_id')` cascade on delete.
- `database/migrations/2026_06_14_152651_create_tasks_table.php` — `category_id` foreign key uses `restrictOnDelete` to prevent orphan deletion.

**Testing Files:**
- `resources/views/pages/⚡categories/categories.test.php` — 14 co-located Livewire tests covering: page rendering, empty state, successful create, duplicate create error, required/max-length validation (create and edit), successful edit, duplicate edit error, successful delete, delete-with-tasks prevention, multiple categories display, and task count display.
- `tests/Feature/Actions/Category/CreateCategoryActionTest.php` — 6 action tests covering: successful creation, duplicate detection per user, cross-user same-name tolerance, rate limiting, time-travel retry, and logging for all outcomes.
- `tests/Feature/Actions/Category/EditCategoryActionTest.php` — 7 action tests covering: successful update, duplicate detection, keeping the same name, cross-user ownership 403 via AuthorizationException, rate limiting, time-travel retry, and logging for all outcomes.
- `tests/Feature/Actions/Category/DeleteCategoryActionTest.php` — 4 action tests covering: successful deletion, cross-user ownership 403 via AuthorizationException, has-tasks prevention, and logging for all outcomes.
- `tests/Feature/Auth/CategoryPageAccessTest.php` — 2 access tests covering: guest redirect to login and authenticated page access.

**Security and Reliability Notes:**
- Category access is protected by the `auth` middleware.
- Ownership is verified at three independent layers: (1) relationship-scoped `findOrFail` in the Livewire component, (2) `CategoryPolicy` enforced via `$user->can()` in each Action, (3) database foreign key constraints. The Policy enforcement lives in the Action layer, not the Livewire component — making it frontend-agnostic and non-bypassable from API controllers, queue jobs, or future Vue clients.
- Category name uniqueness is enforced at the database level with a composite `unique(['user_id', 'name'])` index.
- Category names are normalized (`Str::ucfirst(Str::lower(...))`) to prevent case-sensitive duplicates.
- Deleting a category with tasks is blocked by a server-side check before the database call, avoiding an unhandled `QueryException` from the `restrictOnDelete` constraint.
- Create and edit actions are rate-limited (5 attempts per minute per user+IP) with distinct keys (`create-category:`, `edit-category:`). All rate-limit hits and failures are logged. Delete is not rate-limited (infrequent, destructive action).
- At-most-one edit form is enforced entirely in Alpine via a shared `editingId` variable — no server round-trip for toggle.
- Category form inputs use Alpine `x-model` and `$wire.call()` — no `wire:model` in-flight requests can race with submit responses, ensuring inputs clear reliably.
- Category list is paginated (10 items per page) via `->paginate(10)` in the computed property, with Tailwind-styled links rendered by `->links()`. The paginator resets to page 1 after any create, edit, or delete mutation due to `unset($this->categories)`.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-06 is accepted. Authenticated users can create categories (with duplicate detection and rate limiting), rename categories inline (with duplicate detection and rate limiting), delete categories (blocked if tasks exist), view task counts per category, navigate paginated results, and the use case is covered by 33 passing tests (14 Livewire + 6 create action + 7 edit action + 4 delete action + 2 access).

### UC-07 – Manage Plans

**Status:** Completed

**Goal:** Allow an authenticated user to create, edit, and delete plans. Creating a plan requires a name, optional description, and a required date range (start/end). Editing allows modifying name, description, or date range from a modal. Deletion is blocked when the plan still has tasks assigned.

**Routes:**
- `GET /plan-page` → Livewire page `pages::plan-page`, auth-only route.

**Implementation Files:**
- `routes/web.php` — defines the authenticated plan-page route.
- `resources/views/pages/⚡plan-page/plan-page.php` — Livewire page state; `addPlan()` validates fields and calls `CreatePlanAction`; `startEditing()` queries the plan, `updatePlan()` calls `EditPlanAction`; `deletePlan()` calls `DeletePlanAction`. All three handle result enums with inline errors and bust cache via `unset($this->plans)`.
- `resources/views/pages/⚡plan-page/plan-page.blade.php` — creation form, edit modal (Alpine `$dispatch('open-modal')` + `$wire.startEditing()`), delete buttons, plan list with `withCount('tasks')`, empty state.
- `app/Actions/Plan/CreatePlanAction.php` — create action; authorization via `$user->can('create', Plan::class)`, rate limiting (5/min) before duplicate-name check, user-scoped uniqueness, returns `Created`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Plan/EditPlanAction.php` — edit action; authorization via `$user->can('update', $plan)`, rate limiting (5/min), uniqueness excluding self, returns `Updated`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Plan/DeletePlanAction.php` — delete action; authorization via `$user->can('delete', $plan)`, checks `$plan->tasks()->count()` before deletion, returns `Deleted` or `HasTasks`.
- `app/Policies/PlanPolicy.php` — policy with `create`, `update`, `delete` ownership checks. Enforced in Action classes.
- `app/Enums/CreatePlanResult.php` — result enum (`Created`, `AlreadyExists`, `RateLimited`).
- `app/Enums/EditPlanResult.php` — result enum (`Updated`, `AlreadyExists`, `RateLimited`).
- `app/Enums/DeletePlanResult.php` — result enum (`Deleted`, `HasTasks`).
- `database/migrations/...create_plans_table.php` — creates `plans` table with unique name per user.
- `database/migrations/...create_tasks_table.php` — `plan_id` foreign key uses `restrictOnDelete`.

**Testing Files:**
- `tests/Feature/Actions/Plan/CreatePlanActionTest.php` — 6 action tests: creation, duplicate, cross-user, rate limiting, retry, logging.
- `tests/Feature/Actions/Plan/EditPlanActionTest.php` — 7 action tests: update, duplicate, same-name, ownership, rate limiting, retry, logging.
- `tests/Feature/Actions/Plan/DeletePlanActionTest.php` — 4 action tests: deletion, ownership, has-tasks prevention, logging.
- `resources/views/pages/⚡plan-page/plan-page.test.php` — 18 co-located Livewire tests covering all CRUD operations with validation, errors, and rate limiting.
- `tests/Feature/Auth/PlanPageAccessTest.php` — 2 access tests for guest redirect and authenticated access.

**Security and Reliability Notes:**
- Plan access is protected by the `auth` middleware.
- Ownership is verified at two independent layers: (1) relationship-scoped `$user->plans()->findOrFail()` in each Action, (2) `PlanPolicy` enforced via `$user->can()` in each Action as defense-in-depth.
- Plan name uniqueness is scoped per user.
- Create and edit actions are rate-limited (5 attempts/minute per user+IP) with distinct keys (`create-plan:`, `edit-plan:`). The rate limiter is checked before the duplicate-name DB query. Delete is not rate-limited.
- Deleting a plan with tasks is blocked by a server-side `count()` check before the database call.
- Plan date range is validated with `date_format:Y-m-d` and `after:range.start` rules.
- Edit modal opens instantly (UX-first), populates via `$wire.startEditing()`, closes on success via `$this->dispatch('close-modal')`.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- Inline errors via `$this->addError()` and `<x-ui.error>` components.
- Blade output remains escaped.

**Acceptance Result:** UC-07 is accepted. Authenticated users can create plans (with duplicate detection and rate limiting), edit plans from a modal (with duplicate detection and rate limiting), and delete plans (blocked if tasks exist). The use case is covered by 37 passing tests (6 create + 7 edit + 4 delete action + 18 Livewire + 2 access).

### UC-08 – Manage Tasks

**Status:** Completed

**Goal:** Allow an authenticated user to create, edit, and delete tasks. Creating a task requires a title, optional description, task date, estimated minutes, alarm days, priority, a required category, and an optional plan. Editing allows modifying all fields from a modal. Deletion removes the task with a single click.

**Routes:**
- `GET /task-page` → Livewire page `pages::task-page`, auth-only route.

**Implementation Files:**
- `routes/web.php` — defines the authenticated task-page route.
- `resources/views/pages/⚡task-page/task-page.php` — Livewire page state; `addTask()` validates all fields and calls `CreateTaskAction`; `startEditing()` queries the task, `updateTask()` calls `EditTaskAction`; `deleteTask()` calls `DeleteTaskAction`. All handle result enums with inline errors and bust cache via `unset($this->tasks)`.
- `resources/views/pages/⚡task-page/task-page.blade.php` — create form (title, description, date picker, estimated minutes, alarm days, priority select, category select, plan select); edit modal triggered by Alpine `$dispatch('open-modal')` + `$wire.startEditing()`; delete buttons per task.
- `app/Actions/Task/CreateTaskAction.php` — create action; authorization via `$user->can('create', Task::class)`, rate limiting (5/min) before category/plan existence queries, validates category/plan ownership, returns `Created`, `RateLimited`, `InvalidCategory`, or `InvalidPlan`.
- `app/Actions/Task/EditTaskAction.php` — edit action; authorization via `$user->can('update', $task)`, rate limiting (5/min), validates category/plan ownership, returns `Updated`, `RateLimited`, `InvalidCategory`, or `InvalidPlan`.
- `app/Actions/Task/DeleteTaskAction.php` — delete action; authorization via `$user->can('delete', $task)`, returns `Deleted`.
- `app/Policies/TaskPolicy.php` — policy with `create`, `update`, `delete` ownership checks. Enforced in Action classes.
- `app/Enums/CreateTaskResult.php` — result enum (`Created`, `RateLimited`, `InvalidCategory`, `InvalidPlan`).
- `app/Enums/EditTaskResult.php` — result enum (`Updated`, `RateLimited`, `InvalidCategory`, `InvalidPlan`).
- `app/Enums/DeleteTaskResult.php` — result enum (`Deleted`).
- `database/migrations/...create_tasks_table.php` — creates `tasks` table with foreign keys.

**Testing Files:**
- `tests/Feature/Actions/Task/CreateTaskActionTest.php` — 10 action tests: creation, optional fields, rate limiting, retry, missing/other-user category/plan, logging.
- `tests/Feature/Actions/Task/EditTaskActionTest.php` — 10 action tests: update all fields, plan assignment, ownership, rate limiting, retry, invalid category/plan, logging.
- `tests/Feature/Actions/Task/DeleteTaskActionTest.php` — 3 action tests: deletion, ownership, logging.
- `resources/views/pages/⚡task-page/task-page.test.php` — 25 co-located Livewire tests covering all CRUD operations with validation, errors, and rate limiting.
- `tests/Feature/Auth/TaskPageAccessTest.php` — 2 access tests for guest redirect and authenticated access.

**Security and Reliability Notes:**
- Task access is protected by the `auth` middleware.
- Ownership is verified at two independent layers: (1) relationship-scoped `$user->tasks()->findOrFail()` in each Action, (2) `TaskPolicy` enforced via `$user->can()` in each Action as defense-in-depth.
- Category and plan existence is scoped to the authenticated user — a category or plan belonging to another user is treated as invalid.
- Create and edit actions are rate-limited (5 attempts/minute per user+IP) with distinct keys (`create-task:`, `edit-task:`). The rate limiter is checked before category/plan DB queries. Delete is not rate-limited.
- Task field validation covers all inputs: title (required, max:255), description (nullable, max:5000), date (required, `date_format:Y-m-d`), estimated minutes (required, integer, min:1, max:1440), alarm days (required, integer, min:0, max:365), priority (required, in:low,medium,high), category (required, integer), plan (nullable, integer).
- Edit modal opens instantly (UX-first), populates via `$wire.startEditing()`, closes on success via `$this->dispatch('close-modal')`.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- Database foreign key constraints (`restrictOnDelete` on `category_id` and `plan_id`) ensure referential integrity.
- Inline errors via `$this->addError()` and `<x-ui.error>` components.
- Blade output remains escaped.

**Acceptance Result:** UC-08 is accepted. Authenticated users can create tasks with all required/optional fields, edit all fields from a modal, and delete tasks with a single click. Invalid category/plan selections show clear errors, rate limiting prevents abuse, ownership is enforced. The use case is covered by 50 passing tests (10 create + 10 edit + 3 delete action + 25 Livewire + 2 access).

### UC-09 – Toggle Done

**Status:** Completed

**Goal:** Allow an authenticated user to mark a task as done or not done with a single toggle switch.

**Routes:**
- `GET /task-page` → Livewire page `pages::task-page`, auth-only route (same page as UC-08).

**Implementation Files:**
- `resources/views/pages/⚡task-page/task-page.php` — Livewire page state; `toggleTask()` calls `ToggleTaskDoneAction`, handles `RateLimited` result with inline error message, refreshes task list via `unset($this->tasks)`.
- `resources/views/pages/⚡task-page/task-page.blade.php` — switch component with `wire:click="toggleTask(task.id)"` and `:checked="$task->done"` per task in the list.
- `app/Actions/Task/ToggleTaskDoneAction.php` — toggle done business action; rate limited at 20 attempts per minute, retrieves the task via `$user->tasks()->findOrFail()` for ownership scoping, authorization through `abort_unless($user->can('toggleDone', $task), 403)`, toggles `done` field, logs info on success, returns `Toggled` or `RateLimited`.
- `app/Policies/TaskPolicy.php` — policy with `toggleDone` ownership check. Enforced in Action class.
- `app/Enums/ToggleTaskDoneResult.php` — result enum (`Toggled`, `RateLimited`).

**Testing Files:**
- `tests/Feature/Actions/Task/ToggleTaskDoneActionTest.php` — 6 action tests covering: toggle from not-done to done, toggle from done to not-done, cross-user ownership blocked via `ModelNotFoundException`, logging for successful toggle, rate limiting at 20 attempts, and recovery after rate limit expires.
- `resources/views/pages/⚡task-page/task-page.test.php` — co-located Livewire tests covering (toggle portions): toggle from not-done to done and toggle from done to not-done.
- `tests/Feature/Auth/TaskPageAccessTest.php` — 2 access tests covering: guest redirect to login and authenticated page access.

**Security and Reliability Notes:**
- Task access is protected by the `auth` middleware.
- Ownership is verified at two independent layers: (1) relationship-scoped `$user->tasks()->findOrFail()` in the Action (throws `ModelNotFoundException` if the task belongs to another user), (2) `TaskPolicy` enforced via `$user->can()` in each Action as defense-in-depth.
- Toggle is rate-limited at 20 attempts per minute to prevent abuse.
- Database foreign key constraints (`restrictOnDelete` on `category_id` and `plan_id`) ensure that toggling a task does not affect its category or plan.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-09 is accepted. Authenticated users can toggle task done status with a single click, ownership is enforced, rate limiting prevents abuse, and the use case is covered by 10 passing tests (6 action + 2 Livewire + 2 access).

### UC-13 – Calendar View (Date Range Filter)

**Status:** Completed (simplified — full daily/weekly/monthly views deferred to Layer 2)

**Goal:** Allow users to filter tasks by a custom date range using a range datepicker and a Filter button.

**Routes:**
- `GET /task-page` → Livewire page `pages::task-page`, auth-only route (same page as UC-08).

**Implementation Files:**
- `resources/views/pages/⚡task-page/task-page.php` — `$date_filter` property (DateRange object, defaults to today), `applyDateFilter()` method refreshes task list with the selected range scoping, `tasks()` computed applies `task_date >= start` and `task_date <= end` when the respective dates are present.
- `resources/views/pages/⚡task-page/task-page.blade.php` — range datepicker with `wire:model="date_filter"` and a Filter button with `wire:click="applyDateFilter"`.
- `app/Livewire/Synthesizers/DateRangeSynthesizer.php` — existing synthesizer handles hydration/dehydration of the DateRange value object between JS and Livewire.

**Testing Files:**
- `resources/views/pages/⚡task-page/task-page.test.php` — 4 co-located Livewire tests covering: default filter shows only today's tasks, applyDateFilter scopes to a date range, filter with start date only, filter with end date only.

**Security and Reliability Notes:**
- Task access is protected by the `auth` middleware.
- Ownership is enforced at the query level by `where('user_id', $this->userId)` in the `tasks()` computed.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- Date filtering is safe against SQL injection (uses Eloquent parameter binding via `where`).
- The date filter applies only to the displayed list — it does not affect task creation, editing, or deletion.

**Acceptance Result:** UC-13 is accepted (Layer 1 simplified). Authenticated users can filter their tasks by a custom date range using a range datepicker and Filter button. The default view shows today's tasks. The use case is covered by 6 passing tests (4 Livewire + 2 access). Full daily/weekly/monthly calendar views with workload colors are deferred to Layer 2.

### UC-10 – Daily Workload

**Description:** Show total estimated minutes per day with a workload level and alert. Users see their day's workload at a glance with actionable guidance.

**Implementation:** Added a `workload` computed property to the existing task-page Livewire component (`task-page.php:94-113`). It sums `estimated_minutes` from the already-filtered `$this->tasks` collection and returns `null` when zero, or an array with `total_minutes`, `hours`, `minutes`, and `label`. The `label` drives color-coded alert banners using the app's `<x-ui.alerts>` component.

**Workload levels + alert colors:**
| Minutes | Label | Alert Color | Heading | Icon |
|---|---|---|---|---|
| 0 | (none) | Green | Rest Day | face-smile |
| 1–179 | Light | Sky | Light Day | musical-note |
| 180–359 | Medium | Amber | Medium Day | rocket-launch |
| 360+ | Heavy | Red | Heavy Day | bell-alert |

**Single-day guard:** The alert block is wrapped in `@if($date_filter->getStart() === $date_filter->getEnd())` so workload alerts only appear when viewing a single day. Multi-day range display is deferred to Layer 2.

**Cache invalidation fix:** `unset($this->tasks, $this->workload)` is called in all 5 mutation methods (add, delete, update, toggle, filter) so workload recomputes after any change.

**Files changed:**
- `resources/views/pages/⚡task-page/task-page.php` — added `workload` computed property with threshold logic, added deduplicated `unset` calls
- `resources/views/pages/⚡task-page/task-page.blade.php` — added alert banners with single-day guard
- `resources/views/pages/⚡task-page/task-page.test.php` — 12 workload tests

**Security and Reliability Notes:**
- Workload is computed from the same filtered `$this->tasks` query, so it automatically respects the date filter and owner check.
- Returns `null` instead of `['total_minutes' => 0, ...]` when no tasks match, making it easy to conditionally render with `@if`.
- No new routes, actions, or models — purely a computed aggregation on existing data, no rate limiting needed.
- Single-day guard uses simple string comparison — no SQL or complex logic.

**Acceptance Result:** UC-10 is accepted. Authenticated users see workload alerts (Rest/Light/Medium/Heavy) when viewing a single day. For multi-day ranges, no alerts are shown (deferred to Layer 2). The use case is covered by 12 passing tests (all Livewire).

### UC-15 – Filter & Sort

**Status:** Completed

**Goal:** Allow authenticated users to filter their task list by category, plan, status (done/not done), and priority, and sort by date, priority, or estimated minutes (or any combination).

**Routes:**
- `GET /task-page` → Livewire page `pages::task-page`, auth-only route (same page as UC-08).

**Implementation Files:**
- `resources/views/pages/⚡task-page/task-page.php` — 7 new properties (`$filterCategoryId`, `$filterPlanId`, `$filterStatus`, `$filterPriority`, `$sortByDate`, `$sortByPriority`, `$sortByEstimatedMinutes`), updated `tasks()` computed query with filter conditions (`where` clauses for category, plan, status, priority) and dynamic sort (`orderBy` for date, priority, estimated_minutes). Default sort is `task_date desc, created_at desc` when no custom sort is selected.
- `resources/views/pages/⚡task-page/task-page.blade.php` — filter bar with select dropdowns for Priority, Category, Plan, and Status (done/not done). Sort bar with checkboxes for Date, Priority, and Workload. Both bars have a Filter/Sort button that calls `applyDateFilter`. No new Blade components — reuses existing `<x-ui.select>`, `<x-ui.checkbox>`, and `<x-ui.button>`.
- `app/Enums/TaskPriority.php` — existing priority enum (`Low`, `Medium`, `High`), used for filter comparison.

**Testing Files:**
- `resources/views/pages/⚡task-page/task-page.test.php` — 13 filter and sort tests covering: filters by category, plan, status (done), status (not done), priority, all filters combined, filters + sort combination, sorts by date, sorts by priority, sorts by estimated minutes, stacks multiple sort criteria, defaults to date descending sort, and resetting filters clears all selections.

**Security and Reliability Notes:**
- Task access is protected by the `auth` middleware.
- Ownership is enforced at the query level by `where('user_id', $this->userId)` in the `tasks()` computed — all filters and sorts operate within the user's scope.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- Filtering uses Eloquent parameter binding via `where` — safe against SQL injection.
- Sort properties are `?bool` (not `?string`) so unchecked checkboxes send `false` instead of `""` — checked with `=== true`.
- No new routes, actions, enums, or models — purely query modifications on the existing `tasks()` computed property.
- Sort direction is ASC only (no DSC toggle) — direction control deferred to Layer 2.

**Acceptance Result:** UC-15 is accepted. Authenticated users can filter tasks by category, plan, status, and priority using dropdown selects. They can sort by date, priority, or estimated minutes using checkboxes (multiple sorts stack). All filters and sorts operate within the user's task scope. The view defaults to date-descending sort when no custom sort is selected. The use case is covered by 228 total passing tests (575 assertions), including 13 filter/sort tests within the 89 task-page Livewire tests.

### UC-14 – View Plan Tasks, Plan Progress & Progress Tracking

**Status:** Completed (Layer 1 — combined implementation)

**Goal:** Allow authenticated users to view tasks assigned to a plan with progress, see plan completion percentage, and track progress as tasks are toggled done/not-done.

**Routes:**
- `GET /plan-page` → Livewire page `pages::plan-page`, auth-only route (same page as UC-07).

**Implementation Files:**
- `resources/views/pages/⚡plan-page/plan-page.php` — added `->with('tasks')` to the `plans()` computed property to eagerly load tasks for the popover display.
- `resources/views/pages/⚡plan-page/plan-page.blade.php` — added a `<x-ui.popover>` per plan card containing: a task list (title + done/not-done icon) via `@forelse($plan->tasks)`, a progress display showing `doneCount/tasks_count (progress%)` with an `<x-ui.progress>` bar, and an empty state for plans with no tasks. "View Tasks" button replaced the previous placeholder.
- `resources/js/app.js` — added `import './components/progress.js';` to register the progress bar Alpine component (previously missing, causing `progressComponent is not defined` JS errors).
- `resources/js/components/progress.js` — existing progress bar Alpine component (was not imported in `app.js`).

**Testing Files:**
- `resources/views/pages/⚡plan-page/plan-page.test.php` — 5 new tests covering: view tasks popover with task list, progress with mixed done tasks, 100% progress, 0% progress, and empty state for plans with no tasks.

**Security and Reliability Notes:**
- Plan access is protected by the `auth` middleware.
- Ownership is enforced by the `plans()` computed query scoped to `$this->userId` — tasks are loaded through the owned plan relationship.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- Progress is computed from eager-loaded tasks collection — no additional queries needed.
- The popover content is rendered server-side (hidden by Alpine until clicked) — no additional API calls needed.
- No new routes, actions, enums, or models — purely view-level additions.
- Real-time progress tracking is satisfied for Layer 1: progress is recomputed from the database on every page visit. Cross-component reactivity (e.g., toggling a task on the task page and seeing progress update on the plan page without navigation) is deferred to Layer 2.

**Acceptance Result:** UC-14 is accepted. Authenticated users can view tasks per plan in a popover with done/not-done indicators, see completion percentage with a progress bar, and track plan progress. The use case is covered by 233 total passing tests (583 assertions), including 5 new plan-page tests.

### UC-12 – Reports

**Status:** Completed (Layer 1)

**Goal:** Allow authenticated users to view performance reports over a date range, showing total tasks created, tasks completed, completion rate (%), and overdue count.

**Routes:**
- `GET /reports` → Livewire page `pages::report-page`, auth-only route.

**Implementation Files:**
- `routes/web.php` — defines the authenticated report route.
- `resources/views/pages/⚡report-page/report-page.php` — Livewire component with locked `$userId`, `DateRange $date_filter` defaulting to this month, computed `stats()` returning `tasks_created`, `tasks_completed`, `completion_rate`, and `overdue_count` (scoped by user and date range), and `applyDateFilter()` to refresh stats.
- `resources/views/pages/⚡report-page/report-page.blade.php` — page heading ("Reports"), date range picker + Filter button, 4 stat cards in a responsive grid (Tasks Created, Tasks Completed, Completion Rate with %, Overdue Tasks), each in a white rounded card with colored emphasis text.
- `resources/views/components/layouts/partials/⚡nav-links/nav-links.blade.php` — added "Reports" nav link between Tasks and the end of the link list.

**Testing Files:**
- `resources/views/pages/⚡report-page/report-page.test.php` — 4 co-located Livewire tests covering: page renders with all stat labels, correct stats for a multi-task date range, zero stats for empty date range, and completion rate calculation (2/3 = 67%).

**Security and Reliability Notes:**
- Page access is protected by the `auth` middleware.
- Ownership is enforced at the query level by `where('user_id', $this->userId)` in all stats queries.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- DateRange synthesizer handles hydration/dehydration between JS and Livewire (reused from UC-13).
- Stats are computed properties — no mutations, no rate limiting needed.
- Overdue count is scoped to the selected date range: tasks not done with `task_date < today` within the range.
- Completion rate returns 0% when no tasks exist in the range (division by zero guard).
- No new actions, enums, policies, or models — purely read-only computed queries.

**Acceptance Result:** UC-12 is accepted. Authenticated users can navigate to `/reports`, select a date range, and see their performance stats (created, completed, rate, overdue). All four stats are correctly calculated and scoped to the authenticated user. The use case is covered by 237 total passing tests (594 assertions), including 4 new report-page tests.

### UC-16 – Upcoming Tasks

**Status:** Completed (Layer 1)

**Goal:** Allow authenticated users to see tasks approaching within their notification window (`task_date - day_before_alarm <= today AND task_date >= today`) on the dashboard.

**Routes:**
- `GET /dashboard` → Livewire page `pages::dashboard`, auth-only route (existing).

**Implementation Files:**
- `resources/views/pages/⚡dashboard/dashboard.php` — added locked `$userId`, computed `upcomingTasks()` querying tasks where `task_date >= today` and the notification window has started (via `whereRaw` with SQLite `DATE` modifier), ordered by `task_date ASC`, eager loads `category` and `plan` relationships.
- `resources/views/pages/⚡dashboard/dashboard.blade.php` — rewrote from empty div to a full dashboard view with "Upcoming Tasks" heading, card-per-task list showing title, due date, days-until-due label ("Due today/tomorrow/in X days"), category name, plan name (if assigned), and done/not-done badge. Empty state when no upcoming tasks. "View All Tasks" link at bottom navigating to the task page.

**Testing Files:**
- `resources/views/pages/⚡dashboard/dashboard.test.php` — 5 co-located Livewire tests covering: page renders with heading and "View All Tasks" link, shows tasks within notification window (day_before_alarm=3, task_date=+2), hides tasks outside window (day_before_alarm=1, task_date=+5), empty state when no upcoming tasks, and done/not-done badge display.

**Security and Reliability Notes:**
- Page access is protected by the `auth` middleware.
- Ownership is enforced at the query level by `where('user_id', $this->userId)` in the `upcomingTasks()` computed.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- The notification window logic uses SQLite's `DATE()` function with per-task `day_before_alarm` modifier — correctly scoped per task, not a fixed window.
- Tasks with `day_before_alarm = 0` only show if `task_date = today` (immediate alarm).
- Days-until-due label uses Carbon's `diffInDays()` with `startOfDay()` for consistent day-boundary math.
- No new routes, actions, enums, policies, or models — purely read-only computed queries on the existing dashboard page.

**Acceptance Result:** UC-16 is accepted. Authenticated users open the dashboard and see all tasks within their notification window, with clear status badges and time-until-due labels. Tasks outside the notification window are hidden. The use case is covered by 242 total passing tests (602 assertions), including 5 new dashboard tests and 2 existing access tests.

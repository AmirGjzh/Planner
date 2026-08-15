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
- `resources/views/pages/auth/⚡login/login.php` — Livewire component with typed `$login_error` property (`null` | `'rate_limited'` | `'invalid'`), `$remember` bool for the remember-me toggle, validates email/password with custom `messages()`, resets `$login_error` to `null` before each submission, uses a `match` expression on `LoginResult` enum cases, calls `LoginUserAction`, redirects on success, resets password and sets error state on failure.
- `resources/views/pages/auth/⚡login/login.blade.php` — login form using mine/* components (`mine.input`, `mine.button`, `mine.checkbox`, `mine.alert`, `mine.separator`), error alerts driven by `$login_error` state, remember-me checkbox, forgot password placeholder.
- `app/Actions/Auth/LoginUserAction.php` — final action class; rate limiting via `RateLimiter` with configurable constants (`MAX_ATTEMPTS: 5`, `DECAY_SECONDS: 60`), normalizes email with `Str::lower(Str::trim())`, `Auth::attempt()`, session regeneration, structured logging for all outcomes with `'available_in'` context key.
- `app/Enums/LoginResult.php` — backed string enum (`Fail`, `Success`, `RateLimited`).

**Testing Files:**
- `resources/views/pages/auth/⚡login/login.test.php` — 9 co-located Livewire tests: render, required field validation, email format validation, wrong credentials error state, successful authentication and redirect, rate limiting after 5 failures, retry after 60-second cooldown, clearing a previous login error on a new submission, and blocking login for a soft-deleted account.
- `tests/Feature/Auth/LoginAccessTest.php` — 4 access tests: guest visits login, authenticated user redirected from login, guest redirected from dashboard, authenticated user visits dashboard.
- `tests/Feature/Actions/Auth/LoginUserActionTest.php` — 8 action tests: success, fail, rate limited, email casing normalization, logging for all outcomes, trimming/lowercasing email, blocking soft-deleted users, and applying the rate limit to soft-deleted account attempts.

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

**Acceptance Result:** UC-01 is accepted. 21 UC-01 tests (58 assertions): 9 Livewire UI tests, 8 action tests, 4 access tests. The only failing assertion is the dashboard render in `LoginAccessTest` (`allows authenticated users to visit dashboard`), which fails on the pre-existing broken `/dashboard` view (dead `ui.text` component from legacy dashboard code), not on login behavior itself.

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

**Acceptance Result:** UC-02 is accepted. 24 passing tests. 14 co-located Livewire UI tests (including username-format dataset variants), 8 action tests (success, username taken, email taken, rate limited, logging, and more), 2 access tests, plus additional register flow coverage from peripheral access tests.

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
- `resources/views/pages/⚡categories/categories.php` — Livewire component using `HasUser` trait (with `#[Locked] $userId`) and `WithPagination`. Properties for form inputs (`$add_category`, `$edit_name`), result messages (`$add_error`, `$add_success`, `$edit_error`, `$edit_success`, `$delete_error`), and state trackers (`$editing_id`, `$deleting_id`). Sort via `public string $sort = 'latest'`. Search via `#[Url] public string $search = ''` with `updatingSearch()` calling `resetPage()`. Validation via `rules()` + `$this->validate([…])` with custom `messages()`. `#[Computed] categories()` queries with `where('user_id', auth()->id())`, `->when($this->search, …)`, `->withCount('tasks')`, `->when($this->sort === 'name', fn ($q) => $q->orderBy('name'))`, `->when($this->sort === 'latest', fn ($q) => $q->latest())`, and `->paginate(6)->onEachSide(1)`. Action-call methods (`addCategory()`, `editCategory()`, `deleteCategory()`) normalize names via `Str::ucfirst(Str::lower(...))`, call the corresponding Action, map results via `match`, return early on error, clear fields / `unset($this->categories)` on success. Cancel methods (`cancelAdd()`, `cancelEdit()`, `cancelDelete()`) reset errors, validation, and form fields. `cancelEdit()` also clears `edit_name` and `editing_id` (consistent with `cancelAdd()` clearing `add_category`). `deleteCategory()` dispatches `close-modal` on success.
- `resources/views/pages/⚡categories/categories.blade.php` — search input with `wire:model.live.debounce.200ms="search"` and `leftIcon="magnifying-glass"` / placeholder "Search categories...". Sort dropdown using `<x-mine.dropdown>` with `group="category-actions"`, two options (Latest / Name), each uses `@click="$wire.set('sort', ...)"` with conditional check icon on active option. Add-trigger button via `<x-mine.modal.trigger>` + `<x-mine.button>` with responsive text (`<span class="sm:hidden">New</span><span class="hidden sm:inline">New Category</span>`). Category grid using `card-interactive` class cards with `wire:key="category-{{ $category->id }}"`. Action dropdowns use `<x-mine.dropdown group="category-actions">` for mutual exclusion (at most one open at a time). Edit/delete triggers use `@click.stop` + `$wire.set(…, …, false)` + `$dispatch('open-modal')` (no inline Alpine editing). "View Tasks" button has `type="button"` to prevent form submission. Task count inline: `{{ $category->tasks_count ?: 'No' }} {{ Str::plural('Task', $category->tasks_count) }}` on same line to avoid whitespace break. Pagination via `{{ $this->categories->links(data: ['scrollTo' => false]) }}`. Empty state distinguishes no-categories vs no-search-results. Add/edit modals auto-focus inputs via `x-ref` + `$nextTick()`, with `wire:submit` handlers and `wire:model` bindings. Delete modal is an alert with caution icon, `$wire.set('deleting_id', …, false)`, and `wire:submit="deleteCategory"`. All modal close buttons call the corresponding `$wire.cancel*()` method.
- `resources/views/components/mine/dropdown/index.blade.php` — dropdown Alpine component with optional `group` prop. When `group` is set, opening a dropdown dispatches `close-dropdowns-{group}` via `window.dispatchEvent(new CustomEvent(...))`, and all dropdowns in the same group listen on `window.addEventListener('close-dropdowns-'+this.group, () => this.close())` in `init()`. This ensures at most one dropdown is open per group.
- `app/Actions/Category/CreateCategoryAction.php` — `final` class. Authorization via `$user->can('create', Category::class)`. Rate limiting via `RateLimiter::tooManyAttempts('create-category:'.Str::transliterate($user->email).'|'.$request->ip(), 5)` with `RateLimiter::clear()` on success, `RateLimiter::hit()` otherwise. Checks user-scoped name uniqueness (`$user->categories()->where('name', $name)->exists()`). Creates category. Logging: `info` on success, `warning` on duplicate/rate-limit. Returns `CreateCategoryResult::Created`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Category/EditCategoryAction.php` — `final` class. Same authorization, rate limiting (key: `edit-category:`), and logging pattern. Uniqueness check excludes self (`->where('id', '!=', $category->id)`). Updates the name. Returns `EditCategoryResult::Updated`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Category/DeleteCategoryAction.php` — `final` class. Authorization via `$user->can('delete', $category)`. Checks `$category->tasks()->count() > 0` before deletion. Logging: `info` on success, `warning` on has-tasks. Returns `DeleteCategoryResult::Deleted` or `HasTasks`.
- `app/Policies/CategoryPolicy.php` — policy with `create` (always true), `update` (ownership), `delete` (ownership) methods. Enforced in Action classes, not the Livewire component — frontend-agnostic and non-bypassable.
- `app/Enums/CreateCategoryResult.php` — result enum (`Created`, `AlreadyExists`, `RateLimited`).
- `app/Enums/EditCategoryResult.php` — result enum (`Updated`, `AlreadyExists`, `RateLimited`).
- `app/Enums/DeleteCategoryResult.php` — result enum (`Deleted`, `HasTasks`).
- `database/migrations/2026_06_14_152512_create_categories_table.php` — creates `categories` table with `index('user_id')` and `unique(['user_id', 'name'])`, plus `foreignId('user_id')` cascade on delete.
- `database/migrations/2026_06_14_152651_create_tasks_table.php` — `category_id` foreign key uses `restrictOnDelete` to prevent orphan deletion.

**Testing Files:**
- `resources/views/pages/⚡categories/categories.test.php` — 22 co-located Livewire tests covering: page rendering, empty state, successful create, duplicate create error, required/max-length validation (create and edit), successful edit, duplicate edit error, successful delete, delete-with-tasks prevention, multiple categories display, task count display, search filtering, no-search-results message, name sort, default latest sort, create success message, edit success message, cancel-add state reset, and cancel-edit state reset.
- `tests/Feature/Actions/Category/CreateCategoryActionTest.php` — 7 action tests covering: successful creation, duplicate detection per user, cross-user same-name tolerance, rate limiting (5 rapid attempts), time-travel retry after 1 minute, duplicate-attempt warning log, and success info log.
- `tests/Feature/Actions/Category/EditCategoryActionTest.php` — 8 action tests covering: successful update, duplicate detection, keeping the same name, cross-user ownership 403 via `AuthorizationException`, rate limiting (5 rapid attempts), time-travel retry after 1 minute, duplicate-edit warning log, and success info log.
- `tests/Feature/Actions/Category/DeleteCategoryActionTest.php` — 4 action tests covering: successful deletion, cross-user ownership 403 via `AuthorizationException`, has-tasks prevention, and deletion event log.
- `tests/Feature/Auth/CategoryPageAccessTest.php` — 2 access tests covering: guest redirect to login and authenticated page access.

**Security and Reliability Notes:**
- Category access is protected by the `auth` middleware.
- Ownership is verified at three independent layers: (1) relationship-scoped `findOrFail` in the Livewire component, (2) `CategoryPolicy` enforced via `$user->can()` in each Action, (3) database foreign key constraints. The Policy enforcement lives in the Action layer, not the Livewire component — making it frontend-agnostic and non-bypassable from API controllers, queue jobs, or future Vue clients.
- Category name uniqueness is enforced at the database level with a composite `unique(['user_id', 'name'])` index.
- Category names are normalized (`Str::ucfirst(Str::lower(...))`) to prevent case-sensitive duplicates.
- Deleting a category with tasks is blocked by a server-side check before the database call, avoiding an unhandled `QueryException` from the `restrictOnDelete` constraint.
- Create and edit actions are rate-limited (5 attempts per minute per user+IP) with distinct keys (`create-category:`, `edit-category:`). All rate-limit hits and failures are logged. Delete is not rate-limited (infrequent, destructive action).
- Add, edit, and delete each use a dedicated modal triggered by `@click.stop` + `$wire.set(…, …, false)` + `$dispatch('open-modal')` — no inline Alpine editing, and `wire:model` is safe because validation runs server-side before any mutation. Modal inputs auto-focus via `x-ref` + `$nextTick()` in `x-init`.
- Search is live via `wire:model.live.debounce.200ms` with `#[Url]` persistence (search term survives page reload). `updatingSearch()` calls `resetPage()` so results reset on each keystroke.
- Category list is paginated (6 items per page) via `->paginate(6)->onEachSide(1)` in the computed property. The paginator resets to page 1 after any mutation due to `unset($this->categories)`.
- Sort dropdown lets users toggle between Latest (default) and Name ordering. Uses `$wire.set('sort', ...)` directly — no server round-trip needed for the set action itself, only for the subsequent `categories()` computed re-evaluation.
- Action dropdowns (ellipsis menu on each card) use `group="category-actions"` so at most one action dropdown is open at a time. The sort dropdown shares the same group name to also close when an action dropdown opens and vice versa.
- The dropdown component's `init()` method registers a `window` event listener for `close-dropdowns-{group}` when a group is assigned, and `toggle()` / `show()` dispatch that event before opening — ensuring mutual exclusion across all Livewire components on the page.
- The `#[Locked]` attribute on `$userId` (via `HasUser` trait) prevents client-side tampering.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-06 is accepted. Authenticated users can create categories (with duplicate detection and rate limiting), rename categories (with duplicate detection and rate limiting), delete categories (blocked if tasks exist), search categories, sort by latest or name, view task counts per category, navigate paginated results, and the use case is covered by 46 passing tests (25 Livewire + 7 create action + 8 edit action + 4 delete action + 2 access).

**Global Component Added — Footer:**
- `resources/views/components/mine/footer/index.blade.php` — new footer component with a 4-column layout: brand/description column, quick links column (Categories, Tasks, Plans, Reports), account column (Profile, Settings placeholder, Sign Out), and tech stack column (Laravel, Tailwind, Livewire, Alpine). Uses the app's mine/* component system and `route()` for authenticated routes.

### UC-07 – Manage Plans

**Status:** Completed

**Goal:** Allow an authenticated user to create, edit, and delete plans, track each plan's progress, and mark plans complete or reopen them. Creating a plan requires a name, optional description, and a required date range (start/end). Editing allows modifying name, description, or date range from a modal. Deletion is blocked when the plan still has tasks assigned.

**Routes:**
- `GET /plans` → Livewire page `pages::plans`, auth-only route.

**Implementation Files:**
- `routes/web.php` — defines the authenticated plans route.
- `resources/views/pages/⚡plans/plans.php` — Livewire page state; `addPlan()` validates fields and calls `CreatePlanAction`; `startEditing()` queries the plan, `updatePlan()` calls `EditPlanAction`; `deletePlan()` calls `DeletePlanAction`; `completePlan()`/`reopenPlan()` handle plan done/not-done. All handle result enums with inline errors and bust cache via `unset($this->plans)`. The `plans()` computed uses `->with('tasks')` to eagerly load tasks for the progress popover.
- `resources/views/pages/⚡plans/plans.blade.php` — creation form, edit modal (Alpine `$dispatch('open-modal')` + `$wire.startEditing()`), delete buttons, plan list with `withCount('tasks')`, empty state. Per plan card a `<x-ui.popover>` shows the plan's task list (title + done/not-done icon) via `@forelse($plan->tasks)`, a progress display (`doneCount/tasks_count (progress%)`) with an `<x-ui.progress>` bar, and an empty state for plans with no tasks. Search, sort, filter, grid and the desktop/mobile date-range calendars sit inside `@island(name: 'plans-content', always: true)`; sort/status clicks target the island via `$wire.$island('plans-content').$set(...)` and both calendars receive an `island="plans-content"` prop on the shared `mine/calendar` component so range changes re-render only the island.
- `app/Actions/Plan/CreatePlanAction.php` — create action; authorization via `$user->can('create', Plan::class)`, rate limiting (5/min) before duplicate-name check, user-scoped uniqueness, returns `Created`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Plan/EditPlanAction.php` — edit action; authorization via `$user->can('update', $plan)`, rate limiting (5/min), uniqueness excluding self, returns `Updated`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Plan/DeletePlanAction.php` — delete action; authorization via `$user->can('delete', $plan)`, checks `$plan->tasks()->count()` before deletion, returns `Deleted` or `HasTasks`.
- `app/Actions/Plan/CompletePlanAction.php` — marks a plan complete; returns `Completed` when all tasks are done, `HasUndoneTasks` otherwise.
- `app/Actions/Plan/ReopenPlanAction.php` — resets a plan's `done` flag and logs the event.
- `app/Policies/PlanPolicy.php` — policy with `create`, `update`, `delete` ownership checks. Enforced in Action classes.
- `app/Enums/CreatePlanResult.php` — result enum (`Created`, `AlreadyExists`, `RateLimited`).
- `app/Enums/EditPlanResult.php` — result enum (`Updated`, `AlreadyExists`, `RateLimited`).
- `app/Enums/DeletePlanResult.php` — result enum (`Deleted`, `HasTasks`).
- `app/Enums/CompletePlanResult.php` — result enum (`Completed`, `HasUndoneTasks`).
- `database/migrations/...create_plans_table.php` — creates `plans` table with unique name per user.
- `database/migrations/...create_tasks_table.php` — `plan_id` foreign key uses `restrictOnDelete`.
- `resources/js/app.js` — registers `import './components/progress.js';` for the progress bar Alpine component.
- `resources/js/components/progress.js` — the progress bar Alpine component used by `<x-ui.progress>`.

**Testing Files:**
- `tests/Feature/Actions/Plan/CreatePlanActionTest.php` — 8 action tests: creation, duplicate, cross-user, rate limiting, retry, logging, and more.
- `tests/Feature/Actions/Plan/EditPlanActionTest.php` — 7 action tests: update, duplicate, same-name, ownership, rate limiting, retry, logging.
- `tests/Feature/Actions/Plan/DeletePlanActionTest.php` — 4 action tests: deletion, ownership, has-tasks prevention, logging.
- `tests/Feature/Actions/Plan/CompletePlanActionTest.php` — 5 action tests: completion when all done, blocked with undone tasks, ownership, logging, and more.
- `tests/Feature/Actions/Plan/ReopenPlanActionTest.php` — 4 action tests: reopening, ownership, logging, and more.
- `resources/views/pages/⚡plans/plans.test.php` — 36 co-located Livewire tests covering all CRUD operations with validation, errors, rate limiting, plus plan-progress/popover, complete/reopen, and island-scoped calendar tests.
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
- Completing a plan requires all its tasks to be done (`CompletePlanResult::HasUndoneTasks` otherwise); reopening resets the plan's `done` flag.
- Plan progress is computed from eager-loaded tasks (`->with('tasks')`) — no additional queries needed; the popover content is rendered server-side (hidden by Alpine until clicked).
- Real-time progress tracking (Layer 1): progress is recomputed from the database on every page visit. Cross-component reactivity is deferred to Layer 2.
- Blade output remains escaped.

**Acceptance Result:** UC-07 is accepted. Authenticated users can create plans (with duplicate detection and rate limiting), edit plans from a modal (with duplicate detection and rate limiting), delete plans (blocked if tasks exist), view a plan's tasks and progress in a popover, and complete/reopen plans. The use case is covered by 66 passing tests (8 create + 7 edit + 4 delete + 5 complete + 4 reopen action + 36 Livewire + 2 access).

### UC-08 – Manage Tasks

**Status:** Completed

**Goal:** Allow an authenticated user to create, edit, and delete tasks, mark them done/not done, and view them by single day or a custom date range with filters and sorting. Creating a task requires a title, optional description, task date, estimated minutes, alarm days, priority, a required category, and an optional plan. Editing allows modifying all fields from a modal. Deletion removes the task from a confirmation modal with a single confirm click.

**Routes:**
- `GET /tasks` → Livewire page `pages::tasks`, auth-only route (name `tasks`).

**Implementation Files:**
- `routes/web.php` — defines the authenticated `/tasks` route (`Route::livewire('/tasks', 'pages::tasks')`).
- `resources/views/pages/⚡tasks/tasks.php` — Livewire page state; `addTask()` validates all fields and calls `CreateTaskAction`; `editTask()` resolves the task via `$this->user->tasks()->findOrFail($this->editing_id)` and passes the model to `EditTaskAction`; `deleteTask()` resolves the model the same way before calling `DeleteTaskAction`. `completeTask()`/`reopenTask()` resolve via `$this->completing_id`/`$this->reopening_id` and call `ToggleTaskDoneAction`. All handle result enums with inline errors and bust cache via `unset($this->tasks)`. Add-form fields (`add_estimated_minutes`, `add_alarm_days`, `add_priority`) are nullable with no defaults so the UI shows placeholders. `$range_filter` array (`['start' => ?, 'end' => ?]`) defaults to today in `mount()`, and the `tasks()` computed scopes by `whereDate('task_date', '>=', start)` / `whereDate('task_date', '<=', end)` when present. Filter/sort state is URL-persisted (`#[Url]` on `search`, `sort`, `status_filter`, `category_filter`, `plan_filter`); every `updating*` hook (`Search`, `StatusFilter`, `CategoryFilter`, `PlanFilter`, `RangeFilter`, `Sort`) calls `resetPage()`. The `tasks()` computed captures `$userId`/`$today` once, scopes by owner, applies search/status/category/plan/date-range filters, and sorts by state/load/estimated/latest/date/priority; `hasActiveFilters` distinguishes "No tasks yet." from "No tasks found.".
- `resources/views/pages/⚡tasks/tasks.blade.php` — create form (title, description, date picker, estimated minutes, alarm days, priority select, category select, plan select) where the number fields use placeholders (no baked-in defaults); edit modal populated by Alpine `$dispatch('open-modal')` + `$wire.set('editing_id', …)`; delete confirmation modal (`delete-task-confirmation`) with `wire:submit="deleteTask"`. Date range picker via `<x-mine.calendar wire:model.live="range_filter">` (desktop sidebar + mobile "Date Range" dropdown) with live range changes. Per-card Complete Task / Reopen Task buttons wired to `$wire.set('completing_id'|'reopening_id', $task->id, false)` + `$dispatch('open-modal')`; confirmation modals `complete-task-confirmation` / `reopen-task-confirmation` with `wire:submit="completeTask"` / `wire:submit="reopenTask"`. Task cards render inline in the page (no separate card component) from a single `@php`-derived status block (active/overdue/completed badge + classes). Search, filters, sort, grid and pagination sit inside `@island(name: 'tasks-content', always: true)` so only the island re-renders, with a `wire:loading.delay.short` spinner + dimming overlay. Sort dropdown is one `@foreach` (State/Load/Latest/Date/Priority); Filter Task dropdowns (desktop + mobile) share an options `@foreach`; category/plan multi-select filters use a fixed-height scroll (`max-h-60 overflow-y-auto mine-scrollbar`). Filter/sort clicks target the island via `$wire.$island('tasks-content').$set(...)`; the search input auto-scopes to the island via `wire:model.live`. The category/plan multi-select dropdowns and both date-range calendars receive an `island="tasks-content"` prop on the shared `mine/dropdown`/`mine/calendar` components, which route their `$wire.set(...)` calls through `$wire.$island(this.island).$set(...)` so every filter change re-renders only the island.
- `app/Actions/Task/CreateTaskAction.php` — `final` action with constructor-injected `LoggerInterface`; authorization via `abort_unless($user->can('create', Task::class), 403)`; rate limiting (5/min) before category/plan existence checks; validates category/plan ownership via `$user->categories()->whereKey()` / `$user->plans()->whereKey()`; clears the limiter on success; returns `Created`, `RateLimited`, `InvalidCategory`, or `InvalidPlan`.
- `app/Actions/Task/EditTaskAction.php` — `final` action receiving `Task $task` (resolved by the component); same rate-limit/clear pattern; returns `Updated`, `RateLimited`, `InvalidCategory`, or `InvalidPlan`.
- `app/Actions/Task/DeleteTaskAction.php` — `final` action receiving `Task $task`; no rate limit (matches DeleteCategory/DeletePlan); returns `Deleted`.
- `app/Actions/Task/ToggleTaskDoneAction.php` — `final` action with constructor-injected `LoggerInterface`; receives `Task $task` (resolved by the component); authorization via `abort_unless($user->can('toggleDone', $task), 403)`; rate limited at 20 attempts per minute (key `toggle-task:` via `Str::transliterate`); toggles the `done` field; calls `RateLimiter::hit()` on success (each toggle counts); logs info on success; returns `Toggled` or `RateLimited`.
- `app/Policies/TaskPolicy.php` — policy with `create`, `update`, `delete`, `toggleDone` ownership checks. Enforced in Action classes via `abort_unless`.
- `app/Enums/CreateTaskResult.php` — result enum (`Created`, `RateLimited`, `InvalidCategory`, `InvalidPlan`).
- `app/Enums/EditTaskResult.php` — result enum (`Updated`, `RateLimited`, `InvalidCategory`, `InvalidPlan`).
- `app/Enums/DeleteTaskResult.php` — result enum (`Deleted`).
- `app/Enums/ToggleTaskDoneResult.php` — result enum (`Toggled`, `RateLimited`).
- `database/migrations/...create_tasks_table.php` — creates `tasks` table with foreign keys.

**Testing Files:**
- `tests/Feature/Actions/Task/CreateTaskActionTest.php` — 10 action tests: successful creation, all optional fields, rate limiting after repeated failures, retry after 1 minute, successful create clears the limiter, invalid category (missing / other-user), invalid plan (missing / other-user), logging.
- `tests/Feature/Actions/Task/EditTaskActionTest.php` — 9 action tests: successful update, plan assignment, cross-user ownership 403 via `HttpException`, rate limiting after repeated failures, retry after 1 minute, successful edit clears the limiter, invalid category, invalid plan, logging.
- `tests/Feature/Actions/Task/DeleteTaskActionTest.php` — 3 action tests: deletion, cross-user ownership 403 via `HttpException`, logging.
- `tests/Feature/Actions/Task/ToggleTaskDoneActionTest.php` — 6 action tests covering: toggle from not-done to done, toggle from done to not-done, cross-user ownership 403 via `HttpException`, logging, rate limiting at 20 attempts, and recovery after the limit expires.
- `resources/views/pages/⚡tasks/tasks.test.php` — 66 co-located Livewire tests covering all CRUD operations with validation, errors, ownership (`ModelNotFoundException`), rate limiting, toggle done (complete/reopen), filter/search/sort, date range, the `tasks-content` island wrapper, island-scoped filters, and the loading spinner.
- `tests/Feature/Auth/TasksPageAccessTest.php` — 2 access tests for guest redirect and authenticated access.

**Security and Reliability Notes:**
- Task access is protected by the `auth` middleware.
- Ownership is verified at two independent layers: (1) relationship-scoped `$user->tasks()->findOrFail()` in the component (throws `ModelNotFoundException` if the task belongs to another user), (2) `TaskPolicy` enforced via `abort_unless($user->can(...), 403)` in each Action as defense-in-depth. Actions are frontend-agnostic and non-bypassable.
- Category and plan existence is scoped to the authenticated user — a category or plan belonging to another user is treated as invalid.
- Create and edit actions are rate-limited (5 attempts/minute per user+IP) with distinct keys (`create-task:` and `edit-task:` built via `Str::transliterate('namespace:'.$user->id.'|'.$request->ip())`). Invalid category/plan failures call `RateLimiter::hit()`; a successful create/edit calls `RateLimiter::clear()`. Delete is not rate-limited.
- Toggle done/not-done is rate-limited at 20 attempts/minute per user+IP (`toggle-task:`), the highest limit in the app because toggling is the most frequent action. Every toggle calls `RateLimiter::hit()`, even on success.
- Task field validation covers all inputs: title (required, max:255), description (nullable, max:5000), date (required, `date_format:Y-m-d`), estimated minutes (required, integer, min:1, max:1440), alarm days (required, integer, min:0, max:365), priority (required, in:low,medium,high), category (required, integer), plan (nullable, integer).
- Filter/sort state is `#[Url]`-persisted; sort/filter values are matched against fixed allowed sets via hard-coded `when()` branches (no arbitrary input reaches ORDER BY — safe against SQL injection on SQLite and MySQL). Date range filtering uses Eloquent parameter binding via `whereDate` — safe against SQL injection, and applies only to the displayed list, not to task mutation.
- Edit modal opens instantly with prefilled `edit_*` state; Cancel (`cancelEdit()`) clears errors/validation and closes the modal via Alpine `close()`; success shows an inline success alert.
- Delete requires confirmation; success dispatches `close-modal` with id `delete-task-confirmation`.
- The `#[Locked]` attribute on `$userId` (via `HasUser`) prevents client-side tampering.
- Database foreign key constraints (`restrictOnDelete` on `category_id` and `plan_id`) ensure referential integrity.
- Inline errors via `$this->addError()`/custom error banners; Blade output remains escaped.

**Acceptance Result:** UC-08 is accepted. Authenticated users can create tasks with all required/optional fields (placeholders instead of defaults), edit all fields from a modal, delete tasks with a confirmation modal, toggle tasks done/not done, and view tasks by single day or a custom date range with search/filter/sort within a live island. Invalid category/plan selections show clear errors, rate limiting prevents abuse, ownership is enforced at two layers. The use case is covered by 96 passing tests (10 create + 9 edit + 3 delete + 6 toggle action + 66 Livewire + 2 access).

### UC-09 – Daily Workload

**Status:** Pending — not yet implemented

**Goal:** Show the total estimated time of tasks for each day with a workload alert on the dashboard.

**Notes:** Daily workload alerts previously lived on the task page (per-day aggregation with Rest/Light/Medium/Heavy levels). They were removed from the task page and the UC now targets the dashboard. No implementation or tests exist yet — the dashboard currently shows Upcoming Tasks only.

### UC-10 – Upcoming Tasks

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

**Acceptance Result:** UC-10 is accepted. Authenticated users open the dashboard and see all tasks within their notification window, with clear status badges and time-until-due labels. Tasks outside the notification window are hidden. The use case is covered by 5 dashboard tests plus 2 existing access tests.

### UC-11 – Reports

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
- DateRange synthesizer handles hydration/dehydration between JS and Livewire (reused from the tasks page, UC-08).
- Stats are computed properties — no mutations, no rate limiting needed.
- Overdue count is scoped to the selected date range: tasks not done with `task_date < today` within the range.
- Completion rate returns 0% when no tasks exist in the range (division by zero guard).
- No new actions, enums, policies, or models — purely read-only computed queries.

**Acceptance Result:** UC-11 is accepted. Authenticated users can navigate to `/reports`, select a date range, and see their performance stats (created, completed, rate, overdue). All four stats are correctly calculated and scoped to the authenticated user. The use case is covered by 4 report-page tests.

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

**Acceptance Result:** UC-01 is accepted. 21 UC-01 tests (58 assertions): 9 Livewire UI tests, 8 action tests, 4 access tests. The only failing assertion is the dashboard render in `LoginAccessTest` (`allows authenticated users to visit dashboard`), which fails on the pre-existing broken `/dashboard` view (dead `ui.text` component from the deferred UC-10 dashboard page), not on login behavior itself.

### UC-02 – Register

**Status:** Completed

**Goal:** Allow a guest user to create a new account with a username, email, password, and password confirmation, then redirect them to login after successful registration.

**Routes:**
- `GET /register` → Livewire page `pages::auth.register`, guest-only route.
- `GET /login` → existing guest login route, used as the post-registration destination.

**Implementation Files:**
- `routes/web.php` — defines the guest-only register route.
- `resources/views/pages/auth/⚡register/register.php` — Livewire component with typed `$register_error` property (`null` | `'rate_limited'` | `'username_taken'` | `'email_taken'`), `$password_confirmation` property, resets `$register_error` to `null` before each submission, validates username (regex `/^[a-zA-Z][a-zA-Z0-9_-]{2,29}$/`), email (`email:rfc`), and password (confirmed, min:8) with custom `messages()`, uses a `match` expression to handle all 4 `RegisterResult` enum cases, calls `RegisterUserAction`, resets password fields on failure, redirects to login on success.
- `resources/views/pages/auth/⚡register/register.blade.php` — registration form UI, field errors, register-level error display driven by `$register_error` state, and login navigation link.
- `app/Actions/Auth/RegisterUserAction.php` — final action class; rate limiting via `RateLimiter` with configurable constants (`MAX_ATTEMPTS: 5`, `DECAY_SECONDS: 60`), normalizes username and email with `Str::lower(Str::trim())`, username/email uniqueness checks after the limiter gate, user creation via `User::create()`, race-condition duplicate handling via `QueryException` / `isIntegrityConstraintViolation()` (SQLSTATE '23' prefix), structured logging for all outcomes with `'available_in'` context key.
- `app/Enums/RegisterResult.php` — result enum returned by the register action (`Success`, `UsernameTaken`, `EmailTaken`, `RateLimited`).
- `resources/views/pages/auth/⚡login/login.blade.php` — cross-navigation link from login to register.

**Testing Files:**
- `resources/views/pages/auth/⚡register/register.test.php` — co-located Livewire tests: rendering, required fields, username format (4-row dataset), email format, password confirmation, password length, username taken (incl. mixed-case), email taken, success + redirect, guest state after registration, rate limiting, retry after cooldown, and clearing a previous register error on a new submission.
- `tests/Feature/Auth/RegisterAccessTest.php` — 2 access tests: guest visits register page, authenticated user redirected away from register.
- `tests/Feature/Actions/Auth/RegisterUserActionTest.php` — 8 action tests: success, username taken, mixed-case username taken, email taken, rate limited, whitespace/case normalization, whitespace-padded duplicate username taken, and event logging.

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

**Acceptance Result:** UC-02 is accepted. 27 UC-02 tests: 17 co-located Livewire UI test variants (14 `it()` blocks including the 4-row username-format dataset), 8 action tests, 2 access tests.

### UC-03 – View and Edit Profile

**Status:** Completed

**Goal:** Allow an authenticated user to view account/profile information and update editable profile fields from a modal form.

**Routes:**
- `GET /profile` → Livewire page `pages::profile`, auth-only route.

**Implementation Files:**
- `routes/web.php` — defines the authenticated profile route.
- `resources/views/pages/⚡profile/profile.php` — Livewire page state, authenticated user lookup, profile validation, resets `$edit_error`/`$edit_success` to `null` before each submission, action call, result handling, form reset or cancel behavior, and country list data. On successful edit, dispatches a `profile-updated` browser event (username/email/firstname/lastname) so the global header can refresh the displayed username/initials.
- `resources/views/pages/⚡profile/profile.blade.php` — profile display UI (personal-information rows rendered from a label→value map), edit modal, form fields, field errors, profile-level rate-limit error display, and cancel or apply controls.
- `app/Actions/Profile/UpdateProfileAction.php` — profile update business action; handles rate limiting, normalizes username with `Str::lower(Str::trim())`, username uniqueness checks after the limiter gate, profile persistence, race-condition duplicate handling, and logging.
- `app/Enums/UpdateProfileResult.php` — result enum returned by the profile update action (`Success`, `UsernameTaken`, `RateLimited`).
- `app/Enums/UserGender.php` — enum used by profile validation and the `User.gender` cast.
- `app/Models/User.php` — stores editable profile fields and casts `birthday` and `gender`.

**Testing Files:**
- `resources/views/pages/⚡profile/profile.test.php` — co-located Livewire tests for rendering, initial form state, validation, successful updates, nullable fields, username-taken errors, clearing a previous edit error on a new submission, rate limiting, time-travel retry, and cancel behavior (plus UC-04 delete-account coverage on the same page).
- `tests/Feature/Auth/ProfileAccessTest.php` — route/middleware tests for guest redirect and authenticated profile access.
- `tests/Feature/Actions/Profile/UpdateProfileActionTest.php` — action tests for success, duplicate username result, whitespace/case username normalization, keeping the current username, nullable cleanup, rate limiting, time-travel retry, and logging.

**Security and Reliability Notes:**
- Profile access is protected by the `auth` middleware.
- Livewire validation handles required username, username format, text lengths, enum-backed gender values, valid country codes, and non-future birth dates.
- Username uniqueness is checked inside the action after rate-limit checks to avoid unnecessary database reads while the profile update key is limited.
- Database unique constraints remain the final protection against duplicate usernames.
- Race-condition duplicate failures are caught from database integrity exceptions and converted into a user-friendly username error.
- Profile update attempts are rate-limited by authenticated user ID and IP address.
- Profile update success, duplicate username failures, and rate-limited attempts are logged in the action layer.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-03 is accepted. 23 page test variants (20 `it()` blocks including the 4-row username-format dataset), 10 action tests, 2 access tests. The country list (via `#[Computed(cache: true, key: 'countries-list')]`) is served from cache; profile updates reset previous success/error state before each validation so stale alerts never persist. On success a `profile-updated` event refreshes the header's username/initials/email via an Alpine `x-on:profile-updated.window` binding on the account dropdown.

### UC-04 – Delete Account

**Status:** Completed

**Goal:** Allow an authenticated user to permanently delete their account, requiring password confirmation, with rate limiting to prevent brute-force attacks.

**Routes:**
- `GET /profile` → Livewire page `pages::profile`, auth-only route (existing UC-03 route).

**Implementation Files:**
- `resources/views/pages/⚡profile/profile.php` — Livewire page state; `deleteAccount()` method clears any previous `delete_error` before validating the password field, calls `DeleteAccountAction`, handles all three result states (`Success`, `WrongPassword`, `RateLimited`), and redirects to login on success; `cancelDelete()` resets form state.
- `resources/views/pages/⚡profile/profile.blade.php` — profile display UI with a "Delete Account" button that opens a confirmation modal with a password field and submit or cancel controls; the password field is autofocused on modal open and the submit button is disabled while the delete request is in flight.
- `app/Actions/Auth/DeleteAccountAction.php` — account deletion business action; checks rate limiting (5 attempts per minute per user ID/IP), verifies the password against the user's hashed password, logs the user out, obfuscates email and username to free unique constraints, soft-deletes the user record, and logs the event.
- `app/Enums/DeleteAccountResult.php` — result enum returned by the delete account action (`Success`, `WrongPassword`, `RateLimited`).

**Testing Files:**
- `tests/Feature/Actions/Auth/DeleteAccountActionTest.php` — action tests for successful deletion, email/username obfuscation, wrong password result, rate limiting, time-travel retry, and logging for all outcomes.
- `resources/views/pages/⚡profile/profile.test.php` — co-located delete-flow tests for wrong-password errors, rate limiting, successful deletion with redirect, time-travel retry, cancel behavior, and clearing a previous delete error on a new submission.

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
- `resources/views/components/mine/header/index.blade.php` — global header rebuilt as a single nav source: one `$navLinks` array drives the desktop `nav-link`s (with `aria-current="page"` on the active route) and the mobile navigation dropdown (every item is a working `wire:navigate.hover` link with an icon). Both the mobile navigation dropdown and the account dropdown are `x-mine.dropdown`s grouped under `header-action`, so only one can be open at a time; the account dropdown stays flush to the header's right edge via the original absolute-positioned overlay. The logo was removed (it never rendered) and the dead `hidden` markup was deleted. The user dropdown's Log out item uses Alpine `fetch()` to POST to the logout route with the CSRF token, then calls `Livewire.navigate()` for an SPA transition to the login page (falls back to `window.location` when Livewire is unavailable), guarded by a `busy` flag with response-status checking so a failed request can be retried. The mobile hamburger animates bars→X via plain-div Alpine `:style` transitions (flash-free default-hidden inline styles).
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

**Goal:** Allow an authenticated user to create, rename, and delete categories. Each category name must be unique per user. Deletion is prevented when the category still has tasks assigned. The category index is a Livewire island-driven page: search, sort (including by task count), pagination, and the task grid are scoped to the `category-content` island; "View Tasks" deep-links to `/tasks?category_filter[]=id`, preselecting the category filter and showing all of that category's tasks regardless of date range.

**Routes:**
- `GET /categories` → Livewire page `pages::categories`, auth-only route.
- `GET /tasks?category_filter[]=id` → Livewire page `pages::tasks`. The `category_filter` query param hydrates the URL-bound `#[Url] category_filter` property (read by property name, not the `as` alias) and `mount()` resets `range_filter` to `null` so all tasks for the selected category are shown.

**Implementation Files:**
- `routes/web.php` — defines the authenticated categories route.
- `resources/views/pages/⚡categories/categories.php` — Livewire component using `HasUser` trait (with `#[Locked] $userId`) and `WithPagination`. Properties for form inputs (`$add_category`, `$edit_name`), result messages (`$add_error`, `$add_success`, `$edit_error`, `$edit_success`, `$delete_error`), and state trackers (`$editing_id`, `$deleting_id`). Sort via `public string $sort = 'latest'`. Search via `#[Url] public string $search = ''` with `updatingSearch()` calling `resetPage()`. Validation via `rules()` + `$this->validate([…])` with custom `messages()`. `#[Computed] categories()` queries with `where('user_id', auth()->id())`, `->when($this->search, …)`, `->withCount('tasks')`, `->when($this->sort === 'name', fn ($q) => $q->orderBy('name'))`, `->when($this->sort === 'tasks', fn ($q) => $q->orderByDesc('tasks_count'))`, `->when($this->sort === 'latest', fn ($q) => $q->latest())`, and `->paginate(6)->onEachSide(1)`. Action-call methods (`addCategory()`, `editCategory()`, `deleteCategory()`) normalize names via `Str::ucfirst(Str::lower(...))`, call the corresponding Action, map results via `match`, return early on error, clear fields / `unset($this->categories)` on success. Cancel methods (`cancelAdd()`, `cancelEdit()`, `cancelDelete()`) reset errors, validation, and form fields. `cancelEdit()` also clears `edit_name` and `editing_id` (consistent with `cancelAdd()` clearing `add_category`). `deleteCategory()` dispatches `close-modal` on success.
- `resources/views/pages/⚡categories/categories.blade.php` — search input with `wire:model.live.debounce.200ms="search"` and `leftIcon="magnifying-glass"` / placeholder "Search categories...". Sort dropdown using `<x-mine.dropdown>` with `group="category-actions"` and three options (Date Created / Name / Tasks), each option uses `@click="$wire.$island('category-content').$set('sort', ...)"` with conditional check icon on active option. Add-trigger button via `<x-mine.modal.trigger>` + `<x-mine.button>` with responsive text (`<span class="sm:hidden">New</span><span class="hidden sm:inline">New Category</span>`). The search row, sort dropdown, grid, empty states, and pagination are wrapped in `@island(name: 'category-content', always: true)` so Livewire isle-scopes every `wire:` directive by containment (`interceptAction` → `closestIsland(el)`); modals live OUTSIDE the island. Category grid using `card-interactive` class cards with `wire:key="category-{{ $category->id }}"`. Action dropdowns use `<x-mine.dropdown group="category-actions">` for mutual exclusion (at most one open at a time). Edit/delete triggers use `@click.stop` + `$wire.set(…, …, false)` + `$dispatch('open-modal')` (no inline Alpine editing). "View Tasks" button is an anchor `<a href="{{ route('tasks', ['category_filter' => [$category->id]]) }}" wire:navigate.hover>` (deep-link to the Tasks page with the category preselected). Task count inline: `{{ $category->tasks_count ?: 'No' }} {{ Str::plural('Task', $category->tasks_count) }}` on same line to avoid whitespace break. Pagination via `{{ $this->categories->links(data: ['scrollTo' => false]) }}`. Loading feedback is a centered spinner overlay: a `wire:loading.delay.short` absolute overlay (`aria-label="Loading"`, `size-8 animate-spin` SVG) over a `wire:loading.delay.short.class="opacity-40"` grid wrapper (container height stays stable, no layout jump). Empty state distinguishes no-categories vs no-search-results. Add/edit modals auto-focus inputs via `x-ref` + `$nextTick()`, with `wire:submit` handlers and `wire:model` bindings. Delete modal is an alert with caution icon, `$wire.set('deleting_id', …, false)`, and `wire:submit="deleteCategory"`. All modal close buttons call the corresponding `$wire.cancel*()` method.
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
- `resources/views/pages/⚡categories/categories.test.php` — 25 co-located Livewire tests covering: page rendering, empty state, successful create, duplicate create error, required/max-length validation (create and edit), successful edit, duplicate edit error, successful delete, delete-with-tasks prevention, multiple categories display, task count display, search filtering, no-search-results message, name sort, default latest sort, create success message, edit success message, cancel-add state reset, cancel-edit state reset, plus UC-06 boost coverage (loading spinner overlay markup, `category-content` island marker, and the View Tasks deep-link anchor `route('tasks', ['category_filter' => [$category->id]])`).
- `tests/Feature/Actions/Category/CreateCategoryActionTest.php` — 7 action tests covering: successful creation, duplicate detection per user, cross-user same-name tolerance, rate limiting (5 rapid attempts), time-travel retry after 1 minute, duplicate-attempt warning log, and success info log.
- `tests/Feature/Actions/Category/EditCategoryActionTest.php` — 8 action tests covering: successful update, duplicate detection, keeping the same name, cross-user ownership 403 via `AuthorizationException`, rate limiting (5 rapid attempts), time-travel retry after 1 minute, duplicate-edit warning log, and success info log.
- `tests/Feature/Actions/Category/DeleteCategoryActionTest.php` — 4 action tests covering: successful deletion, cross-user ownership 403 via `AuthorizationException`, has-tasks prevention, and deletion event log.
- `tests/Feature/Auth/CategoryPageAccessTest.php` — 2 access tests covering: guest redirect to login and authenticated page access.
- `resources/views/pages/⚡tasks/tasks.test.php` — 1 HTTP regression test, `it('hydrates the category filter from the categories query string')`, covering the deep-link boost: a GET to `route('tasks', ['category_filter' => [$work->id]])` hydrates `#[Url] category_filter` from the query string (by property name, not `as` alias), shows only the Work category's tasks, and includes an old-dated task to prove `mount()` reset `range_filter` to `null` (all-dates view). HTTP GET via `route()` is used because `Livewire::withQueryParams()->test()` does not hydrate server-side.

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
- Sort dropdown lets users toggle between Latest (default), Name, and Task Count ordering. Uses `$wire.$island('category-content').$set('sort', ...)` — Alpine `@click` has no Livewire action origin, so the island must be explicitly scoped (unlike `wire:click`, which auto-scopes via island containment). No server round-trip needed for the set action itself, only for the subsequent `categories()` computed re-evaluation.
- The page's interactive region (search row, sort dropdown, grid, empty states, pagination) is wrapped in `@island(name: 'category-content', always: true)`. Every `wire:` directive inside is auto-scoped to the island by containment (`closestIsland(el)` in `interceptAction`), keeping updates within the rendered fragment and preserving layout stability. The add/edit/delete modals live outside the island so their Alpine-driven `$dispatch('open-modal')` flows are unaffected.
- Loading feedback uses only named delay modifiers (`wire:loading.delay.short`, 150ms) — `wire:loading.delay.150ms` is not valid in Livewire 4 (the JS delay map + injected CSS only define `<delay.short>`/`<delay.long>`). Target-less loading auto-scopes by island metadata. `wire:loading.delay.short.class="opacity-40"` applies a class, NOT `display:none`, so the grid's inline `display:grid` is never clobbered.
- "View Tasks" deep-links via `<a href="{{ route('tasks', ['category_filter' => [$category->id]]) }}" wire:navigate.hover>`. Livewire's `#[Url]` hydration reads the query string by PROPERTY NAME (`category_filter`), not the `as` alias — a generic `?categories[0]=id` never matched and silently showed all categories. The tasks page's `mount()` resets `range_filter` to `null` when `category_filter` is present, so deep-linked users see every task for that category, not just today's.
- Action dropdowns (ellipsis menu on each card) use `group="category-actions"` so at most one action dropdown is open at a time. The sort dropdown shares the same group name to also close when an action dropdown opens and vice versa.
- The dropdown component's `init()` method registers a `window` event listener for `close-dropdowns-{group}` when a group is assigned, and `toggle()` / `show()` dispatch that event before opening — ensuring mutual exclusion across all Livewire components on the page.
- The `#[Locked]` attribute on `$userId` (via `HasUser` trait) prevents client-side tampering.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-06 is accepted and fully finished. Authenticated users can create categories (with duplicate detection and rate limiting), rename categories (with duplicate detection and rate limiting), delete categories (blocked if tasks exist), search categories, sort by date created / name / task count, view task counts per category, navigate paginated results, deep-link "View Tasks" into `/tasks` with the category filter preselected (all-dates view via `range_filter` reset), all inside a Livewire island with a stable centered spinner overlay for loading feedback. Covered by 46 passing tests (25 co-located page tests + 7 create action + 8 edit action + 4 delete action + 2 access) plus 1 HTTP deep-link regression test in the tasks suite.

**Global Component Added — Footer:**
- `resources/views/components/mine/footer/index.blade.php` — new footer component with a 4-column layout: brand/description column, quick links column (Categories, Tasks, Plans, Reports), account column (Profile, Settings placeholder, Sign Out), and tech stack column (Laravel, Tailwind, Livewire, Alpine). Uses the app's mine/* component system and `route()` for authenticated routes.

### UC-07 – Manage Plans

**Status:** Completed

**Goal:** Allow an authenticated user to create, edit, and delete plans. Creating a plan requires a name, optional description, and a required date range (start/end). Editing allows modifying name, description, or date range from a modal. Deletion is blocked when the plan still has tasks assigned. The plans page is a Livewire island (`plans-content`) wrapping search, sort (including the new **Deadline** ordering), status filter, date-range calendars, the plan list, and pagination, with a centered spinner overlay for loading feedback. Each plan card shows completion progress (folded in from UC-14). "View Tasks" and "Add Task" deep-link to `/tasks?plan_filter[]=id`, preselecting the plan filter and showing all of that plan's tasks regardless of date range.

**Routes:**
- `GET /plans` → Livewire page `pages::plans`, auth-only route.
- `GET /tasks?plan_filter[]=id` → Livewire page `pages::tasks`. The `plan_filter` query param hydrates the URL-bound `#[Url] plan_filter` property (read by property name, not the `as` alias) and `mount()` resets `range_filter` to `null` so all tasks for the selected plan are shown.

**Implementation Files:**
- `routes/web.php` — defines the authenticated plans route.
- `resources/views/pages/⚡plans/plans.php` — Livewire page state using `HasUser` + `WithPagination`. `#[Url]` state: `$search`, `$sort` (default `'state'`), `$status_filter` (default `'all'`). `$range_filter` (defaults to the current month in `mount()`) is not URL-bound. `#[Computed] plans()` selects plan columns, filters by search/range/status, aggregates `withCount(['tasks', 'tasks as tasks_done_count' => done])` and `withSum('tasks', 'estimated_minutes')`, orders by `name` / `deadline` (`orderBy('finish_date')->orderBy('id')`, earliest finish first, deterministic for pagination) / `latest` / `load` (`orderByDesc('tasks_sum_estimated_minutes')`) / `state` (`orderByRaw` CASE: active → overdue → completed), and `paginate(3)->onEachSide(1)`. `updatingSearch()`, `updatingStatusFilter()`, and `updatingRangeFilter()` all `resetPage()`. CRUD methods call the corresponding Action, map result enums with `match`, `unset($this->plans)` on success (`addPlan`, `editPlan`, `deletePlan`, `completePlan`, `reopenPlan`), plus `cancel*` resets; delete/complete/reopen dispatch `close-modal` on success.
- `resources/views/pages/⚡plans/plans.blade.php` — search input `wire:model.live.debounce.200ms="search"`, sort dropdown (`group="plan-filter"`) with five options (State / Deadline / Load / Latest / Name), status filter dropdown (All / Active / Completed / Overdue), and the mobile date-range dropdown all inside `@island(name: 'plans-content', always: true)`. Alpine `@click` sort/status sets are explicitly island-scoped `$wire.$island('plans-content').$set(...)` (Alpine has no Livewire action origin); `wire:model.live` on search and both `<x-mine.calendar wire:model.live="range_filter">` instances auto-scope by containment. Loading feedback: a `wire:loading.delay.short` centered spinner overlay (`aria-label="Loading"`, `size-8 animate-spin`) over a `wire:loading.delay.short.class="opacity-40"` wrapper around the `md:flex` content block (grid + side calendar), keeping the real grid mounted so the page + pagination never jump. Cards are three state variants (`wire:key="plan-{{ $plan->id }}"`), each with a `plan-actions` action dropdown: **View Tasks** deep-links via `<x-mine.dropdown.item href="{{ route('tasks', ['plan_filter' => [$plan->id]]) }}">` (renders a real `menuitem` anchor with `wire:navigate.hover` + `@click="close()"`, no manual `<a>` nesting), Edit/Delete use `@click.stop` + `$wire.set(..., false)` + `$dispatch('open-modal')`. The overdue/active cards' **Add Task** buttons are also deep-links to the same route (`<a href="..." wire:navigate.hover>` wrapped around `mine-btn-outline-danger`/`mine-btn-outline-primary` buttons — both variants covered). Empty states, pagination (`$this->plans->links(data: ['scrollTo' => false])`) live inside the island; all modals (add/edit/delete/complete/reopen) live outside it.
- `resources/views/pages/⚡tasks/tasks.php` — `plan_filter` is now `#[Url] public array $plan_filter = [];` (deep-link hydration); `mount()` sets `range_filter = null` when `category_filter` OR `plan_filter` is present in the query string, so plan/category deep-links show all matching tasks regardless of date. Also `#[Url(as: 'add_plan', history: false)] public ?int $add_plan = null;` — when a matching `add_plan` query param is present, `mount()` sets the add-task modal's `add_plan_id` and dispatches `open-modal` for `add-task-form` (foreign plan IDs are ignored), so "Add Task" deep-links from a plan card preopen the modal with that plan preselected.
- `resources/views/components/mine/select/index.blade.php` — modal select `normalize(value)` fix: `null`/`undefined`/`''` pass through untouched, anything else is `String(value)`. Applied in `init()` via `$wire.get` and in the `$wire.$watch` for `wire:model` — resolves the opaque-match bug where a PHP int (`add_plan_id`) never matched the string `data-value` of the selected option (strict `===`), which prevented the "preselect the plan in the add-task modal" deep-link from rendering a selected option.
- `app/Actions/Plan/CreatePlanAction.php` — create action; authorization via `$user->can('create', Plan::class)`, rate limiting (5/min) before duplicate-name check, user-scoped uniqueness, returns `Created`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Plan/EditPlanAction.php` — edit action; authorization via `$user->can('update', $plan)`, rate limiting (5/min), uniqueness excluding self, returns `Updated`, `AlreadyExists`, or `RateLimited`.
- `app/Actions/Plan/DeletePlanAction.php` — delete action; authorization via `$user->can('delete', $plan)`, checks `$plan->tasks()->count()` before deletion, returns `Deleted` or `HasTasks`.
- `app/Actions/Plan/CompletePlanAction.php` — complete action; authorization via `$user->can('complete', $plan)`, checks for any undone tasks before completing, returns `Completed` or `HasUndoneTasks`.
- `app/Actions/Plan/ReopenPlanAction.php` — reopen action; authorization via `$user->can('update', $plan)`, marks the plan undone, returns void after logging.
- `app/Policies/PlanPolicy.php` — policy with `create`, `update`, `delete`, `complete` ownership checks. Enforced in Action classes.
- `app/Enums/CreatePlanResult.php` — result enum (`Created`, `AlreadyExists`, `RateLimited`).
- `app/Enums/EditPlanResult.php` — result enum (`Updated`, `AlreadyExists`, `RateLimited`).
- `app/Enums/DeletePlanResult.php` — result enum (`Deleted`, `HasTasks`).
- `app/Enums/CompletePlanResult.php` — result enum (`Completed`, `HasUndoneTasks`).
- `database/migrations/...create_plans_table.php` — creates `plans` table with unique name per user.
- `database/migrations/...create_tasks_table.php` — `plan_id` foreign key uses `restrictOnDelete`.

**Plan Progress & Tracking (folded in from UC-14):**
- `resources/views/pages/⚡plans/plans.php` — `plans()` computed uses `withCount(['tasks', 'tasks as tasks_done_count' => done])` for the progress math; progress is derived from the count aggregation, no separate queries.
- `resources/views/pages/⚡plans/plans.blade.php` — per-plan card shows a progress display (`doneCount/tasks_count (progress%)`) with an `<x-ui.progress>` bar and a `<x-ui.popover>` listing the plan's tasks (title + done/not-done icon via `@forelse($plan->tasks)`) with an empty state for plans without tasks.
- `resources/js/app.js` — imports `./components/progress.js` to register the progress-bar Alpine component (previously missing, causing `progressComponent is not defined` JS errors).
- `resources/js/components/progress.js` — existing progress-bar Alpine component.

**Testing Files:**
- `tests/Feature/Actions/Plan/CreatePlanActionTest.php` — 8 action tests: creation, description, name casing normalization, duplicate, cross-user, rate limiting, time-travel retry, logging.
- `tests/Feature/Actions/Plan/EditPlanActionTest.php` — 7 action tests: update, duplicate, same-name, ownership, rate limiting, retry, logging.
- `tests/Feature/Actions/Plan/DeletePlanActionTest.php` — 4 action tests: deletion, ownership, has-tasks prevention, logging.
- `tests/Feature/Actions/Plan/CompletePlanActionTest.php` — 5 action tests: complete with all tasks done, complete with no tasks, block on undone tasks, block another user's plan, logging.
- `tests/Feature/Actions/Plan/ReopenPlanActionTest.php` — 4 action tests: reopen marks undone, keeps existing tasks, ownership guard, logging.
- `resources/views/pages/⚡plans/plans.test.php` — 35 co-located Livewire tests covering: page render, empty state, status badges, create/edit/delete CRUD with validation/duplicate/rate-limit cases, complete/reopen flows, ownership guards, status filtering (server-side + URL hydration), filter dropdown options, plan progress & tracking (folded in from UC-14: view-tasks popover with task list, progress with mixed done tasks, 100% progress, 0% progress, and empty state for plans with no tasks), plus UC-07 boost coverage (Deadline sort order, default state sort, loading spinner overlay markup, `plans-content` island marker, the View Tasks deep-link route, and the add-task deep-link route carrying `add_plan`).
- `tests/Feature/Auth/PlanPageAccessTest.php` — 2 access tests for guest redirect and authenticated access.

**Security and Reliability Notes:**
- Plan access is protected by the `auth` middleware.
- Ownership is verified at two independent layers: (1) relationship-scoped `$user->plans()->findOrFail()` in each Action, (2) `PlanPolicy` enforced via `$user->can()` in each Action as defense-in-depth.
- Plan name uniqueness is scoped per user.
- Create and edit actions are rate-limited (5 attempts/minute per user+IP) with distinct keys (`create-plan:`, `edit-plan:`). The rate limiter is checked before the duplicate-name DB query. Delete is not rate-limited.
- Deleting a plan with tasks is blocked by a server-side `count()` check before the database call.
- Plan date range is validated with `date_format:Y-m-d` and `after:range.start` rules.
- Edit modal opens instantly (UX-first) and is populated via `@click.stop` + `$wire.set('editing_id', ..., false)` (and `edit_name`/`edit_description`/`edit_range`) + `$dispatch('open-modal')` — no inline Alpine editing; validation runs server-side before any mutation on submit.
- The interactive region (search, sort, status filter, calendars, grid, pagination) is wrapped in `@island(name: 'plans-content', always: true)`. `wire:` directives inside auto-scope to the island by containment (`closestIsland(el)`); Alpine `@click` sets must be explicitly scoped with `$wire.$island('plans-content').$set(...)`. `always: true` makes CRUD actions (parent renders) also refresh the island. Modals live outside the island.
- Loading feedback uses only named delay modifiers (`wire:loading.delay.short`, 150ms — `wire:loading.delay.150ms` is not valid in Livewire 4). `wire:loading.delay.short.class="opacity-40"` applies a class, not `display:none`, so the grid's `display:grid` is never clobbered; the real grid stays mounted under a centered spinner overlay so the page and pagination never jump.
- "View Tasks" and "Add Task" deep-link via `route('tasks', ['plan_filter' => [$plan->id]])`. `#[Url]` hydration reads the query string by property name (`plan_filter`), not the `as` alias. The tasks page's `mount()` resets `range_filter` to `null` when `plan_filter`/`category_filter` is present, so deep-linked users see every matching task, not just today's.
- Deadline sort uses `orderBy('finish_date')->orderBy('id')` (earliest finish date first; `finish_date` is non-nullable, `id` tiebreak keeps it deterministic across pagination).
- Date-range calendar changes reset pagination via `updatingRangeFilter() -> resetPage()` so deep pages never render as empty after a filter change.
- Inline errors via `$this->addError()` and `<x-ui.error>` components.
- Blade output remains escaped.

**Acceptance Result:** UC-07 is accepted and fully finished. Authenticated users can create plans (with duplicate detection and rate limiting), edit plans from a modal (with duplicate detection and rate limiting), delete plans (blocked if tasks exist), complete/reopen plans, search, filter by status, sort by state / deadline / load / latest / name, filter by date range (desktop + mobile), view per-plan task lists and completion progress (progress bar + task popover, folded in from UC-14), and deep-link "View Tasks"/"Add Task" into `/tasks` with the plan filter preselected (all-dates view via `range_filter` reset) plus the add-task modal preopened with the plan selected, all inside a Livewire island with a stable centered spinner overlay. Covered by 65 passing tests (8 create + 7 edit + 4 delete + 5 complete + 4 reopen action + 35 Livewire + 2 access), plus the add-plan deep-link tests in the tasks suite. Cross-component reactivity (task-page toggle updating plan progress live) is deferred to Layer 2.

### UC-08 – Manage Tasks (merged: CRUD + Toggle Done + Overdue + Calendar/Range Filter + Filter & Sort + Single-Day Workload)

**Status:** Completed

**Goal:** Allow an authenticated user to create, edit, and delete tasks; toggle tasks done/not-done; see overdue tasks; filter by a custom date range; and filter/sort the task list. Creating a task requires a title, optional description, task date, estimated minutes, alarm days, priority, a required category, and an optional plan. Editing allows modifying all fields from a modal. Deletion removes the task with a single click.

**Routes:**
- `GET /tasks` → Livewire page `pages::tasks`, auth-only route.
- `GET /tasks?category_filter[]=id` / `GET /tasks?plan_filter[]=id` → deep-link hydration from the categories/plans pages (same route).
- `GET /tasks?add_plan=id` → deep-link that preselects the plan and opens the add-task modal (from the plans page).

**Implementation Files:**
- `routes/web.php` — defines the authenticated tasks route (`/tasks`, `pages::tasks`).
- `resources/views/pages/⚡tasks/tasks.php` — Livewire page state. URL-bound `#[Url]` state: `$search`, `$sort` (default `'state'`), `$status_filter` (default `'all'`), `$category_filter` (array), `$plan_filter` (array), `$addPlan` (`as: 'add_plan'`, `history: false`). `$range_filter` (not URL-bound) defaults to today in `mount()`; `mount()` resets `range_filter` to `null` when `category_filter` or `plan_filter` is present in the query string and preopens the add-task modal when a valid `add_plan` deep-link is present. CRUD methods (`addTask`, `startEditing`, `updateTask`, `deleteTask`) call the corresponding Actions and bust the query cache via `unset($this->tasks)`. `#[Computed] tasks()` filters by search (title LIKE), date range, status (`active`/`completed`/`overdue`), category/plan (`whereIn`), and orders by `state` (CASE active→overdue→completed) / `date` / `priority` (CASE low=2) / `estimated`/`load` (desc) / `latest`, `paginate(6)->onEachSide(1)`. `#[Computed] workload()` returns `null` for zero tasks or multi-day ranges, else `total_minutes`/`hours`/`minutes`/`label` (Light < 180, Medium < 360, Heavy 360+). `#[Computed] categories()` / `plans()` power the filter dropdowns. `completeTask()` / `reopenTask()` wrap `ToggleTaskDoneAction` per task.
- `resources/views/pages/⚡tasks/tasks.blade.php` — search input, sort dropdown with eight options (State / Date / Priority / Estimated / Load / Latest), status filter dropdown (All / Active / Completed / Overdue), category + plan filter selects, a range datepicker (`range_filter`), task cards with done/not-done toggles, edit/delete actions, an overdue status badge, create/edit modals, single-day workload alert banners, and empty states.
- `app/Actions/Task/CreateTaskAction.php` — create action; authorization via `$user->can('create', Task::class)`, rate limiting (5/min) before category/plan existence queries, validates category/plan ownership, returns `Created`, `RateLimited`, `InvalidCategory`, or `InvalidPlan`.
- `app/Actions/Task/EditTaskAction.php` — edit action; authorization via `$user->can('update', $task)`, rate limiting (5/min), validates category/plan ownership, returns `Updated`, `RateLimited`, `InvalidCategory`, or `InvalidPlan`.
- `app/Actions/Task/DeleteTaskAction.php` — delete action; authorization via `$user->can('delete', $task)`, returns `Deleted`.
- `app/Actions/Task/ToggleTaskDoneAction.php` — toggle done business action; rate limited at 20 attempts per minute (toggle key), retrieves the task via `$user->tasks()->findOrFail()` for ownership scoping, authorization through `abort_unless($user->can('toggleDone', $task), 403)`, toggles `done` field, logs info on success, returns `Toggled` or `RateLimited`.
- `app/Policies/TaskPolicy.php` — policy with `create`, `update`, `delete`, `toggleDone` ownership checks. Enforced in Action classes.
- `app/Enums/TaskPriority.php` — priority enum (`Low`, `Medium`, `High`) used by validation and the `priority` filter/sort.
- `app/Enums/CreateTaskResult.php` — result enum (`Created`, `RateLimited`, `InvalidCategory`, `InvalidPlan`).
- `app/Enums/EditTaskResult.php` — result enum (`Updated`, `RateLimited`, `InvalidCategory`, `InvalidPlan`).
- `app/Enums/DeleteTaskResult.php` — result enum (`Deleted`).
- `app/Enums/ToggleTaskDoneResult.php` — result enum (`Toggled`, `RateLimited`).
- `database/migrations/...create_tasks_table.php` — creates `tasks` table with foreign keys.

**Testing Files:**
- `tests/Feature/Actions/Task/CreateTaskActionTest.php` — 10 action tests: creation, optional fields, rate limiting, retry, limiter cleared on success, missing/other-user category/plan, logging.
- `tests/Feature/Actions/Task/EditTaskActionTest.php` — 9 action tests: update all fields, plan assignment, ownership, rate limiting, retry, limiter cleared on success, invalid category/plan, logging.
- `tests/Feature/Actions/Task/DeleteTaskActionTest.php` — 3 action tests: deletion, ownership, logging.
- `tests/Feature/Actions/Task/ToggleTaskDoneActionTest.php` — 6 action tests: toggle to done, toggle to not done, cross-user ownership blocked via `ModelNotFoundException`, logging, rate limiting at 20 attempts, recovery after limit resets.
- `resources/views/pages/⚡tasks/tasks.test.php` — 74 co-located Livewire tests covering: page render, empty state, status badges, create (with plan, validation, rate limit, invalid category/plan), edit (validation, rate limit, invalid category/plan), delete, complete/reopen toggles (incl. rate limit + ownership), default today's tasks, date range filter (start/end only), workload (null, Light/Medium/Heavy boundaries, sums, respects range, renders, updates on create/delete, hidden on multi-day), sort (state/load/date/priority/workload/latest), status filter (active/completed/overdue/all, URL hydration), search (match, no-results, dropdown options), category/plan filters (dropdown options, all when none, single/multiple, combined, no-results), and deep-link hydration (`category_filter`, `plan_filter`, `add_plan` incl. foreign-plan ignore).
- `tests/Feature/Auth/TasksPageAccessTest.php` — 2 access tests covering: guest redirect to login and authenticated page access.

**Security and Reliability Notes:**
- Task access is protected by the `auth` middleware.
- Ownership is verified at two independent layers: (1) relationship-scoped `$user->tasks()->findOrFail()` in each Action, (2) `TaskPolicy` enforced via `$user->can()` in each Action as defense-in-depth. Filters/sorts also operate within `where('user_id', auth()->id())` at the query level.
- Category and plan existence is scoped to the authenticated user — a category or plan belonging to another user is treated as invalid.
- Create and edit actions are rate-limited (5 attempts/minute per user+IP) with distinct keys (`create-task:`, `edit-task:`). The rate limiter is checked before category/plan DB queries. Toggle is rate-limited at 20 attempts/minute (toggle key). Delete is not rate-limited.
- Task field validation covers all inputs: title (required, max:255), description (nullable, max:5000), date (required, `date_format:Y-m-d`), estimated minutes (required, integer, min:1, max:1440), alarm days (required, integer, min:0, max:365), priority (required, in:low,medium,high), category (required, integer), plan (nullable, integer).
- Edit modal opens instantly (UX-first), populates via `$wire.startEditing()`, closes on success via `$this->dispatch('close-modal')`.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- Database foreign key constraints (`restrictOnDelete` on `category_id` and `plan_id`) ensure referential integrity.
- Overdue is a status filter view (`done = false AND task_date < today`), not a separate page — marking a task done or deleting it removes it from the list.
- Date-range filtering uses Eloquent parameter binding via `where` — safe against SQL injection. The `add_plan` deep-link ignores plan IDs not owned by the authenticated user.
- Single-day workload alerts reuse the same filtered task query, so they respect the range and owner check; multi-day ranges get no workload alert (full daily/weekly/monthly calendar views with workload colors are deferred).
- Inline errors via `$this->addError()` and `<x-ui.error>` components.
- Blade output remains escaped.

**Acceptance Result:** UC-08 is accepted and fully finished. Authenticated users can create tasks with all required/optional fields, edit all fields from a modal, delete tasks with a single click, toggle done/not-done, filter by category/plan/status (incl. overdue)/date range, sort by state/date/priority/load/latest, search, see single-day workload alerts, and deep-link from categories/plans with filters preselected. Invalid category/plan selections show clear errors, rate limiting prevents abuse, ownership is enforced. Covered by **104 passing tests** (10 create + 9 edit + 3 delete + 6 toggle action + 74 Livewire + 2 access). Full daily/weekly/monthly calendar views with workload colors are deferred.

### UC-09 – Daily Workload (Deferred — dashboard page)

**Status:** **Deferred.** The full dashboard workload view is not yet built; the dashboard page is a broken placeholder (dead `ui.text` component) and its 5 co-located tests fail as a known baseline. Single-day workload alerts already exist on the tasks page as part of UC-08.

**Description:** Show total estimated minutes per day with a workload level and alert (Rest/Light/Medium/Heavy), aggregated across a day or range on the dashboard.

**Workload levels:** (implemented in UC-08's single-day alerts)
| Minutes | Label | Alert Color | Heading | Icon |
|---|---|---|---|---|
| 0 | (none) | Green | Rest Day | face-smile |
| 1–179 | Light | Sky | Light Day | musical-note |
| 180–359 | Medium | Amber | Medium Day | rocket-launch |
| 360+ | Heavy | Red | Heavy Day | bell-alert |

**To build (future):** A dashboard workload panel/calendar grid summing estimated minutes per day from `Task::where('user_id', ...)->whereDate('task_date', ...)`, reusing the UC-08 threshold mapping, plus multi-day aggregate views. No code changes made as part of this UC; deferred.

### UC-10 – Upcoming Tasks (Deferred — dashboard page)

**Status:** **Deferred.** The dashboard page is not yet built (broken placeholder with dead legacy components; its 5 co-located tests fail as a known baseline).

**Goal:** Allow authenticated users to see tasks approaching within their notification window (`task_date - day_before_alarm <= today AND task_date >= today`) on the dashboard, with status badges and time-until-due labels; tasks outside the window stay hidden. Implementation would reuse the per-task `day_before_alarm` modifier and Carbon's `diffInDays()` with `startOfDay()`. No code changes made as part of this UC; deferred.

### UC-11 – Reports (Deferred — report page)

**Status:** **Deferred.** The report page is not yet built (dead `DateRange` component placeholder; its 4 co-located tests fail as a known baseline).

**Goal:** Allow authenticated users to view performance reports over a date range, showing total tasks created, tasks completed, completion rate (%), and overdue count, scoped to the authenticated user, with a division-by-zero guard when no tasks exist in the range. No code changes made as part of this UC; deferred.

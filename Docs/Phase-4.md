# Phase 4 – Iterative Feature Development

## Objective

Implement each User Story completely, from design to testing and documentation, before moving to the next story.

---

## 4.1 – Select a User Story

For each iteration:

- Choose one User Story from the Phase-1 docs
- Review its requirements
- Review its Acceptance Criteria
- Define its Definition of Done

**Deliverable:** Fully understood story with clear success criteria.

---

## 4.2 – UX/UI Design

Design only what is needed for the current story.

### UX Flow

Define:
- Happy path
- Error paths
- Loading states
- Empty states

### Wireframe

Sketch (on paper or Figjam):
- Page layout
- Components
- Navigation flow

### Design System Usage

- Reuse existing components whenever possible
- Create new reusable components if necessary

**Deliverable:** Implementable UX/UI design.

---

## 4.3 – Technical Design

Before coding:

### Backend Design

Define:
- Domain entities involved
- Services needed
- Actions needed (if any)
- Validation rules
- Authorization rules

### Frontend Design (Livewire)

Define:
- Full-page or nested Livewire component
- Component properties
- Component actions (methods)
- Blade view structure

**Deliverable:** Technical implementation plan.

---

## 4.4 – Backend Implementation

Implement:

- Service classes
- Action classes
- Model scopes or accessors (if needed)
- Validation logic
- Authorization (Gates / Policies)

Apply:
- SRP (Single Responsibility Principle)
- DRY (Don't Repeat Yourself)
- Separation of Concerns
- Phase-3 naming conventions

**Deliverable:** Working backend functionality.

---

## 4.5 – Frontend Implementation

Implement:

- Livewire component class
- Blade view(s)
- Forms with validation
- User interactions (toggle done, filter, sort, etc.)

Verify:
- Responsiveness
- Error handling
- Loading states
- Empty states

**Deliverable:** Working user-facing feature.

---

## 4.6 – Testing

### Unit Tests

Test:
- Business rules (e.g., workload calculation, overdue detection)
- Domain logic in Services / Actions

### Integration Tests

Test:
- Livewire component behavior
- Database interactions
- End-to-end feature flow (where applicable)

### Manual Testing

Verify:
- Happy paths
- Edge cases
- Failure scenarios

**Deliverable:** Tested feature.

---

## 4.7 – Code Review & Refactoring

Review:
- Readability
- Simplicity
- Consistency with Phase-3 conventions
- Maintainability

Perform:
- Small refactors
- Cleanup
- Dead code removal

**Deliverable:** Clean and maintainable code.

---

## 4.8 – Documentation

Update:
- Architecture notes
- Technical decisions
- Phase-3 patterns (if new patterns emerge)

**Deliverable:** Up-to-date documentation.

---

## 4.9 – Story Acceptance

Verify:
- Acceptance Criteria satisfied
- Definition of Done satisfied
- Tests passing
- Documentation updated

**Outcome:**

- ✅ **Story Completed** — move to the next story

or

- 🔄 **Return to previous steps** for corrections

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
- `GET /dashboard` → temporary authenticated destination after login, auth-only route.

**Implementation Files:**
- `Planner/routes/web.php` — defines guest login route and authenticated dashboard route.
- `Planner/resources/views/pages/auth/⚡login/login.php` — Livewire page state, validation, action call, error handling, and redirect.
- `Planner/resources/views/pages/auth/⚡login/login.blade.php` — login form UI, field errors, credential/rate-limit error display, remember-me option.
- `Planner/app/Actions/Auth/LoginUserAction.php` — login business action; handles rate limiting, authentication attempt, session regeneration, and logging.
- `Planner/app/Enums/LoginResult.php` — result enum returned by the login action (`Success`, `Fail`, `RateLimited`).

**Testing Files:**
- `Planner/resources/views/pages/auth/⚡login/login.test.php` — co-located Livewire tests for rendering, validation, failed login, successful login, and rate limiting.
- `Planner/tests/Feature/Auth/LoginAccessTest.php` — route/middleware tests for guest and authenticated access.
- `Planner/tests/Feature/Actions/Auth/LoginUserActionTest.php` — action tests for result states and login logging.
- `Planner/tests/Pest.php` — Pest base setup for Feature and co-located Livewire tests with `LazilyRefreshDatabase`.
- `Planner/phpunit.xml` — adds the `Components` test suite for `resources/views/**/*.test.php`.

**Security & Reliability Notes:**
- Password validation is handled server-side by Livewire.
- Failed credentials use a generic error message.
- Login attempts are rate-limited by normalized email and IP address.
- Successful login regenerates the session.
- Login success, failed attempts, and rate-limited attempts are logged in the action layer.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-01 is accepted. Dashboard content and the future homepage/root route are intentionally deferred to later use cases.

### UC-02 – Register

**Status:** Completed

**Goal:** Allow a guest user to create a new account with a username, email, password, and password confirmation, then redirect them to login after successful registration.

**Routes:**
- `GET /register` → Livewire page `pages::auth.register`, guest-only route.
- `GET /login` → existing guest login route, used as the post-registration destination.

**Implementation Files:**
- `Planner/routes/web.php` — defines the guest-only register route.
- `Planner/resources/views/pages/auth/⚡register/register.php` — Livewire page state, validation, action call, result handling, password reset on failure, and redirect.
- `Planner/resources/views/pages/auth/⚡register/register.blade.php` — registration form UI, field errors, register-level error display, password reveal inputs, and login navigation link.
- `Planner/app/Actions/Auth/RegisterUserAction.php` — registration business action; handles rate limiting, username/email uniqueness checks after the limiter gate, user creation, race-condition duplicate handling, and logging.
- `Planner/app/Enums/RegisterResult.php` — result enum returned by the register action (`Success`, `UsernameTaken`, `EmailTaken`, `RateLimited`).
- `Planner/resources/views/pages/auth/⚡login/login.blade.php` — adds navigation from login to register.
- `Planner/resources/views/pages/auth/⚡login/login.php` — small validation/message cleanup kept aligned with the auth flow.

**Testing Files:**
- `Planner/resources/views/pages/auth/⚡register/register.test.php` — co-located Livewire tests for rendering, validation, duplicate username/email errors, successful registration, guest state after registration, and rate limiting.
- `Planner/tests/Feature/Auth/RegisterAccessTest.php` — route/middleware tests for guest and authenticated access.
- `Planner/tests/Feature/Actions/Auth/RegisterUserActionTest.php` — action tests for success, duplicate results, rate limiting, password hashing, and logging.

**Security & Reliability Notes:**
- Registration validation is handled server-side by Livewire.
- Email inputs are validated as RFC email addresses without browser-only validation.
- Passwords are stored through the `User` model's hashed cast.
- Username and email uniqueness are checked inside the action after rate-limit checks to avoid unnecessary database reads while an IP is limited.
- Database unique constraints remain the final protection against duplicate accounts.
- Race-condition duplicate failures are caught from database integrity exceptions and converted into user-friendly username/email errors.
- Failed duplicate attempts are rate-limited by IP address.
- Registration success, duplicate failures, and rate-limited attempts are logged in the action layer.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-02 is accepted. Registration creates an account, redirects guests to login, does not auto-login the new user, handles duplicate data cleanly, and is covered by passing tests.

### UC-03 – View and Edit Profile

**Status:** Completed

**Goal:** Allow an authenticated user to view account/profile information and update editable profile fields from a modal form.

**Routes:**
- `GET /profile` → Livewire page `pages::profile`, auth-only route.

**Implementation Files:**
- `Planner/routes/web.php` — defines the authenticated profile route.
- `Planner/resources/views/pages/⚡profile/profile.php` — Livewire page state, authenticated user lookup, profile validation, action call, result handling, form reset/cancel behavior, and country list data.
- `Planner/resources/views/pages/⚡profile/profile.blade.php` — profile display UI, edit modal, form fields, field errors, profile-level rate-limit error display, and cancel/apply controls.
- `Planner/app/Actions/Profile/UpdateProfileAction.php` — profile update business action; handles rate limiting, username uniqueness checks after the limiter gate, profile persistence, race-condition duplicate handling, and logging.
- `Planner/app/Enums/UpdateProfileResult.php` — result enum returned by the profile update action (`Success`, `UsernameTaken`, `RateLimited`).
- `Planner/app/Enums/UserGender.php` — enum used by profile validation and the `User.gender` cast.
- `Planner/app/Models/User.php` — stores editable profile fields and casts `birth_date` and `gender`.

**Testing Files:**
- `Planner/resources/views/pages/⚡profile/profile.test.php` — co-located Livewire tests for rendering, initial form state, validation, successful updates, nullable fields, username-taken errors, rate limiting, time-travel retry, and cancel behavior.
- `Planner/tests/Feature/Auth/ProfileAccessTest.php` — route/middleware tests for guest redirect and authenticated profile access.
- `Planner/tests/Feature/Actions/Profile/UpdateProfileActionTest.php` — action tests for success, duplicate username result, keeping the current username, nullable cleanup, rate limiting, time-travel retry, and logging.

**Security & Reliability Notes:**
- Profile access is protected by the `auth` middleware.
- Livewire validation handles required username, username format, text lengths, enum-backed gender values, valid country codes, and non-future birth dates.
- Username uniqueness is checked inside the action after rate-limit checks to avoid unnecessary database reads while the profile update key is limited.
- Database unique constraints remain the final protection against duplicate usernames.
- Race-condition duplicate failures are caught from database integrity exceptions and converted into a user-friendly username error.
- Profile update attempts are rate-limited by authenticated user ID and IP address.
- Profile update success, duplicate username failures, and rate-limited attempts are logged in the action layer.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-03 is accepted. Authenticated users can view and edit profile information, invalid input is rejected, duplicate usernames are handled cleanly, rate limiting is enforced, and the use case is covered by passing tests.

### UC-04 – Logout

**Status:** Completed

**Goal:** Allow an authenticated user to log out, ending their session, and transition back to the login page using an SPA navigation without a full page reload.

**Routes:**
- `POST /logout` — plain POST route within the `auth` middleware group, returns 204 No Content.

**Implementation Files:**
- `Planner/routes/web.php` — defines the authenticated POST `/logout` route with a named route `logout`; executes `LogoutUserAction` and returns `response()->noContent()`.
- `Planner/resources/views/layouts/app.blade.php` — user dropdown menu; the Log out item uses Alpine `fetch()` to POST to the logout route with the CSRF token, then calls `Livewire.navigate()` for an SPA transition to the login page.
- `Planner/app/Actions/Auth/LogoutUserAction.php` — logout business action; retrieves the authenticated user ID, calls `Auth::logout()`, invalidates the session, regenerates the CSRF token, and logs the event.

**Testing Files:**
- `Planner/tests/Feature/Auth/LogoutAccessTest.php` — route/middleware tests for successful logout (asserts 204 No Content and guest state) and guest redirect to login.

**Security & Reliability Notes:**
- Logout is protected by the `auth` middleware; guests are redirected to login.
- Session is invalidated and the CSRF token is regenerated after logout to prevent session fixation.
- Logout events are logged with the user ID and IP address.
- The Alpine fetch approach avoids a full page reload — the 204 response is consumed silently and `Livewire.navigate()` provides an SPA transition to the login page.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-04 is accepted. Authenticated users can log out with a single click, the session is properly invalidated, and the user is transitioned to the login page without a full browser refresh.

### UC-05 – Delete Account

**Status:** Completed

**Goal:** Allow an authenticated user to permanently delete their account, requiring password confirmation, with rate limiting to prevent brute-force attacks.

**Routes:**
- `GET /profile` → Livewire page `pages::profile`, auth-only route (existing UC-03 route).

**Implementation Files:**
- `Planner/resources/views/pages/⚡profile/profile.php` — Livewire page state; `deleteAccount()` method validates the password field, calls `DeleteAccountAction`, handles all three result states (`Success`, `WrongPassword`, `RateLimited`), and redirects to login on success; `cancelDelete()` resets form state.
- `Planner/resources/views/pages/⚡profile/profile.blade.php` — profile display UI with a "Delete Account" button that opens a confirmation modal with a password field and submit/cancel controls (unchanged from UC-03).
- `Planner/app/Actions/Auth/DeleteAccountAction.php` — account deletion business action; checks rate limiting (5 attempts per minute per user ID/IP), verifies the password against the user's hashed password, logs the user out, obfuscates email and username to free unique constraints, soft-deletes the user record, and logs the event.
- `Planner/app/Enums/DeleteAccountResult.php` — result enum returned by the delete account action (`Success`, `WrongPassword`, `RateLimited`).

**Testing Files:**
- `Planner/tests/Feature/Actions/Auth/DeleteAccountActionTest.php` — action tests for successful deletion, email/username obfuscation, wrong password result, rate limiting, time-travel retry, and logging for all outcomes.

**Security & Reliability Notes:**
- Account deletion is protected by the `auth` middleware via the profile route.
- Password verification is performed inside the action layer, not the Livewire component — ensuring the guard applies regardless of caller.
- Rate limiting (5 attempts per minute) prevents brute-force password guessing on the delete account flow.
- Email and username are obfuscated with `deleted-user-{id}` / `deleted_user_{id}` before soft-deleting, freeing unique constraints for future registrations without leaking the original values.
- Database unique constraints remain the final protection against duplicate data for the obfuscated values (rare but safe).
- Deletion success, wrong password attempts, and rate-limited attempts are logged in the action layer.
- The action logs the user out and invalidates the session before deleting the user record.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-05 is accepted. Authenticated users can delete their account with password confirmation, wrong passwords are rejected with a clear error, brute-force attempts are rate-limited, the account is properly obfuscated and soft-deleted, and the use case is covered by passing tests.

### UC-10 – Manage Categories

**Status:** Completed

**Goal:** Allow an authenticated user to create, rename, and delete categories. Each category name must be unique per user. Deletion is prevented when the category still has tasks assigned.

**Routes:**
- `GET /category-page` → Livewire page `pages::category-page`, auth-only route.

**Implementation Files:**
- `Planner/routes/web.php` — defines the authenticated category-page route.
- `Planner/resources/views/pages/⚡category-page/category-page.php` — Livewire page state, validation via `Validator::make()` (no `wire:model` — race-condition safe), action calls for create/edit/delete, result handling for `AlreadyExists`/`RateLimited`/`HasTasks`, paginated categories computed property, and `unset($this->categories)` cache busting after mutations.
- `Planner/resources/views/pages/⚡category-page/category-page.blade.php` — category creation form with Alpine `x-model` + `$wire.call()` (no `wire:model` race conditions), category list with `withCount('tasks')`, per-category inline edit form with Alpine `x-show` toggled by a parent-scoped `editingId` (at most one open simultaneously, no server round-trip), delete buttons with `wire:click`, pagination links via `$this->categories->links()`, and empty state.
- `Planner/app/Actions/Category/CreateCategoryAction.php` — create category business action; authorization through `abort_unless($user->can('create', ...), 403)`, rate limiting (5 attempts/minute per user+IP) with logging (info on success, warning on errors), checks user-scoped name uniqueness, creates the category, returns `Created`, `AlreadyExists`, or `RateLimited`.
- `Planner/app/Actions/Category/EditCategoryAction.php` — edit category business action; authorization through `abort_unless($user->can('update', ...), 403)`, rate limiting (5 attempts/minute per user+IP) with logging (info on success, warning on errors), checks uniqueness excluding self, updates the name, returns `Updated`, `AlreadyExists`, or `RateLimited`.
- `Planner/app/Actions/Category/DeleteCategoryAction.php` — delete category business action; authorization through `abort_unless($user->can('delete', ...), 403)`, logging (info on success, warning on errors), checks for assigned tasks before deletion, returns `Deleted` or `HasTasks`.
- `Planner/app/Policies/CategoryPolicy.php` — policy with `create` (always true), `update` (ownership), `delete` (ownership) methods. Enforced in Action classes, not the Livewire component — frontend-agnostic and non-bypassable.
- `Planner/app/Enums/CreateCategoryResult.php` — result enum (`Created`, `AlreadyExists`, `RateLimited`).
- `Planner/app/Enums/EditCategoryResult.php` — result enum (`Updated`, `AlreadyExists`, `RateLimited`).
- `Planner/app/Enums/DeleteCategoryResult.php` — result enum (`Deleted`, `HasTasks`).
- `Planner/database/migrations/2026_06_14_152512_create_categories_table.php` — creates `categories` table with `index('user_id')` and `unique(['user_id', 'name'])`, plus `foreignId('user_id')` cascade on delete.
- `Planner/database/migrations/2026_06_14_152651_create_tasks_table.php` — `category_id` foreign key uses `restrictOnDelete` to prevent orphan deletion.

**Testing Files:**
- `Planner/resources/views/pages/⚡category-page/category-page.test.php` — 14 co-located Livewire tests covering: page rendering, empty state, successful create, duplicate create error, required/max-length validation (create + edit), successful edit, duplicate edit error, successful delete, delete-with-tasks prevention, multiple categories display, and task count display.
- `Planner/tests/Feature/Actions/Category/CreateCategoryActionTest.php` — 6 action tests covering: successful creation, duplicate detection per user, cross-user same-name tolerance, rate limiting, time-travel retry, and logging for all outcomes.
- `Planner/tests/Feature/Actions/Category/EditCategoryActionTest.php` — 7 action tests covering: successful update, duplicate detection, keeping the same name, cross-user ownership 403 via AuthorizationException, rate limiting, time-travel retry, and logging for all outcomes.
- `Planner/tests/Feature/Actions/Category/DeleteCategoryActionTest.php` — 4 action tests covering: successful deletion, cross-user ownership 403 via AuthorizationException, has-tasks prevention, and logging for all outcomes.

**Security & Reliability Notes:**
- Category access is protected by the `auth` middleware.
- Ownership is verified at three independent layers: (1) relationship-scoped `findOrFail` in the Livewire component, (2) `CategoryPolicy` enforced via `$user->can()` in each Action, (3) database foreign key constraints. The Policy enforcement lives in the Action layer, not the Livewire component — making it frontend-agnostic and non-bypassable from API controllers, queue jobs, or future Vue clients.
- Category name uniqueness is enforced at the database level with a composite `unique(['user_id', 'name'])` index.
- Category names are normalized (`Str::ucfirst(Str::lower(...))`) to prevent case-sensitive duplicates.
- Deleting a category with tasks is blocked by a server-side check before the database call, avoiding an unhandled `QueryException` from the `restrictOnDelete` constraint.
- Create and edit actions are rate-limited (5 attempts per minute per user+IP) with distinct keys (`create-category:`, `edit-category:`). All rate-limit hits and failures are logged. Delete is not rate-limited (infrequent, destructive action).
- At-most-one edit form is enforced entirely in Alpine via a shared `editingId` variable — no server round-trip for toggle.
- Category form inputs use Alpine `x-model` + `$wire.call()` — no `wire:model` in-flight requests can race with submit responses, ensuring inputs clear reliably.
- Category list is paginated (10 items per page) via `->paginate(10)` in the computed property, with Tailwind-styled links rendered by `->links()`. The paginator resets to page 1 after any create/edit/delete mutation due to `unset($this->categories)`.
- The `#[Locked]` attribute on `$userId` prevents client-side tampering.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-10 is accepted. Authenticated users can create categories (with duplicate detection and rate limiting), rename categories inline (with duplicate detection and rate limiting), delete categories (blocked if tasks exist), view task counts per category, navigate paginated results, and the use case is covered by 31 passing tests (14 Livewire + 6 create action + 7 edit action + 4 delete action).

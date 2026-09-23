# Phase 4 – Iterative Feature Development

## Objective

Implement each User Story completely — design to test to documentation — before moving to the next story.

## 4.1 – Build

Combine UX/UI, backend, and frontend into a single implementation step.

- Review the Phase-1 user story, its acceptance criteria, and the Phase-2 UC flow
- Reuse existing Livewire, `mine/*` components, and Blade layouts
- Create the Action class with rate limiting, ownership checks, and result enums
- Create the Livewire component (full-page or nested) with validation and error handling
- Create the Blade view with the appropriate states (empty, loading, error)

**Deliverable:** Working feature.

## 4.2 – Test

Cover all layers:

- **Action tests** — all result states, rate limiting, ownership, logging
- **Livewire tests** — co-located in `resources/views/pages/⚡{page}/{page}.test.php` covering render, validation, success, and error paths
- **Access tests** — guest redirect and authenticated access in `tests/Feature/Auth/`
- Run `vendor/bin/pint --dirty --format agent` before finalizing

**Deliverable:** Passing tests.

## 4.3 – Refactor

Review the implementation for:

- Readability and simplicity
- Consistency with existing patterns (check sibling files)
- No dead code or leftover debug statements

**Deliverable:** Clean code.

## 4.4 – Document (current convention)

Add a compact UC entry below the Completed Use Cases heading (Status, Goal, Delivered scope, Key decisions, Tests). Do **not** keep a dated build/test/refactor journal — durable content lives in the structure and `.ai/rules`, and recoverable history lives in git. The full dated journal from the original UC-01–11 build was compressed and moved to git history.

**Deliverable:** Updated Phase-4 delivered-scope entries.

## 4.5 – Accept

Verify:

- Acceptance Criteria from Phase-1 are satisfied
- All tests pass (`php artisan test --compact --filter={testName}`)
- Documentation is current

**Outcome:** Story completed — move to the next UC in topological order.

## Phase Completion Criteria

- All User Stories are completed
- All Acceptance Criteria pass
- All tests pass
- Documentation is current
- The application is deployable

## Completed Use Cases

All use cases are delivered, tested, and accepted. Files and structure referenced below are load-bearing; behavior contract details live in `.ai/rules/`.

### UC-01 – Login

**Status:** Completed

**Goal:** Email + password sign-in with localized validation/credential errors, then the authenticated dashboard.

**Delivered:** `app/Actions/Auth/LoginUserAction.php` (+ `LoginResult` enum), Livewire page `pages::auth.login`, guest-only route, remember-me toggle (opt-in), success toast survives the redirect via flashed session + `mount()` re-dispatch. The action sets the app locale from the signed-in user (`users.locale`), since guest pages have no `HasUser`.

**Key decisions:** rate limit key `login:{ip}` (IP-only, 5/60s, `Str::transliterate` guard); generic credential error (no user enumeration); soft-deleted accounts blocked; session regenerated on success; structured logging with `available_in` context.

**Tests:** Livewire page, action, and access coverage.

### UC-02 – Register

**Status:** Completed

**Goal:** Guest account creation (username, email, password + confirmation), then redirect to login.

**Delivered:** `app/Actions/Auth/RegisterUserAction.php` (+ `RegisterResult` enum), Livewire page `pages::auth.register`, guest-only route. Username regex `/^[a-zA-Z][a-zA-Z0-9_-]{2,29}$/`, `email:rfc`, password `confirmed|min:8`. Success flashes a "your account is ready" toast; only the **login** page re-dispatches flash toasts (register has no `mount()` — the earlier duplicate there was removed as dead code).

**Key decisions:** rate limit key `register:{ip}` (IP-only, 5/60s); uniqueness checked after the limiter gate to avoid DB reads while limited; race duplicates caught from DB integrity constraint exceptions into friendly duplicates; passwords stored via the `User` model hashed cast.

**Tests:** Livewire page, action, and access coverage.

### UC-03 – View and Edit Profile

**Status:** Completed

**Goal:** View and update personal info plus a Theme and Language preference from a modal.

**Delivered:** `app/Actions/Profile/UpdateProfileAction.php` (+ `UpdateProfileResult` enum), Livewire page `pages::profile`, `app/Enums/UserGender.php`, `User::initials()` (multibyte-safe), `users.locale` persisted. Country list cached per locale and ICU-collated; birthday renders Jalali in `fa`; on theme/locale change the page re-lands on `/profile` with `navigate: false` so the new language applies. `profile-updated` event carries `initials` from the server (no Alpine reimplementation). `deleteAccount()` resets locale to `config('app.locale')` before redirecting home.

**Key decisions:** locale restricted to `in:['fa','en']`, theme validated against `config('themes.name')`; `HasUser::boot()` applies `users.locale` app-wide on every Livewire request; rate limit key `update-profile:` (user-ID+IP shape); clearable nullable fields handled by the action (they set empty strings to `null`).

**Tests:** Livewire page, action, access, and unit coverage.

### UC-04 – Delete Account

**Status:** Completed

**Goal:** Permanent account deletion with password confirmation and rate limiting.

**Delivered:** `app/Actions/Auth/DeleteAccountAction.php` (+ `DeleteAccountResult` enum), confirmation modal on the profile page. Obfuscates email/username (`deleted-user-{id}` / `deleted_user_{id}`) to free unique constraints, then soft-deletes; logs the user out and invalidates the session.

**Key decisions:** password verified inside the action (frontend-agnostic); rate limit 5/min per user+IP; success flashes a toast re-dispatched by the home page's `mount()`.

**Tests:** action coverage plus the delete-account flow in the profile Livewire tests.

### UC-05 – Logout

**Status:** Completed

**Goal:** One-click logout with an SPA transition back to home.

**Delivered:** `POST /logout` (204 No Content) + `app/Actions/Auth/LogoutUserAction.php` (returns `void` — the single-case `LogoutResult` enum was removed as YAGNI). Header dropdown logs out via Alpine `fetch()` with CSRF, then `Livewire.navigate()` to home; flashed toast is re-dispatched by home's `mount()`.

**Key decisions:** session invalidated + CSRF regenerated (fixation prevention); action logs user ID and IP.

**Tests:** access and action coverage.

### UC-06 – Manage Categories

**Status:** Completed

**Goal:** Create, rename, delete, search, and sort categories; names unique per user; deletion blocked when tasks are assigned.

**Delivered:** `CreateCategoryAction`/`EditCategoryAction`/`DeleteCategoryAction` (+ matching result enums), `app/Policies/CategoryPolicy.php`, Livewire page `pages::categories`. Grid inside `@island(name: 'category-content', always: true)`; search `LOWER(name) LIKE ?` (case-insensitive, collation-independent); sorts (`latest`/`name`/`tasks` via `withCount`); paginate 6/onEachSide(1); action dropdowns grouped (`group="category-actions"`) for mutual exclusion; "View Tasks" deep-link preselects `category_filter`; empty state distinguishes no-categories vs no-results.

**Key decisions:** name normalization lives only in the actions (`Str::ucfirst(Str::lower())`) — the component takes raw validated input; DB composite `unique(['user_id','name'])` is the final guard; `restrictOnDelete` FK + a server-side `count()` check before delete (avoids unhandled `QueryException`); rate limit keys `create-category:` / `edit-category:` (5/min user+IP, cleared on success) — delete not rate-limited; add/edit/delete each use a dedicated modal (`@click.stop` + `$wire.set(…, false)` + `$dispatch('open-modal')`), never inline Alpine editing; `#[Url]` search/sort + `resetPage()` on updates.

**Tests:** Livewire page, action, and access coverage.

### UC-07 – Manage Plans

**Status:** Completed

**Goal:** Create, edit, delete, track progress, complete, and reopen plans; names unique per user; deletion blocked when tasks are assigned.

**Delivered:** `CreatePlanAction`/`EditPlanAction`/`DeletePlanAction`/`CompletePlanAction`/`ReopenPlanAction` (+ matching result enums), `PlanPolicy`, `Jalali::monthBounds()`, Livewire page `pages::plans`. Cards inside `@island(name: 'plans-content', always: true)`; case-insensitive search; status filter (all/active/completed/overdue) + sort (state/deadline/load/name/latest); paginate 3/onEachSide(1); desktop + mobile `<x-mine.calendar>` for the month-range filter; per-plan task counts eager-loaded (`withCount`); progress bars via `<x-mine.progress>`.

**Key decisions:** default range filter is the current month (Jalali in `fa`, Gregorian in `en`) via shared `defaultRangeFilter()`; `end.after start` date validation; complete is blocked until all tasks done (`HasUndoneTasks`), reopen resets the `done` flag; rate limit keys `create-plan:` / `edit-plan:` (5/min) — delete and complete/reopen not limited; status accent colors are literal Tailwind classes (v4 scanner-safe, no dynamic concatenation); locale-aware card dates (`d MMM ، y` in `fa`); success actions dispatch toasts (`variant: success|info`, 3000ms, bottom-center).

**Tests:** Livewire page, action, and access coverage.

### UC-08 – Manage Tasks

**Status:** Completed

**Goal:** Create, edit, delete, toggle done, and browse tasks by single day or custom range with filters/sorts; category required, plan optional.

**Delivered:** `CreateTaskAction`/`EditTaskAction`/`DeleteTaskAction`/`ToggleTaskDoneAction` (+ matching result enums), `TaskPolicy`, Livewire page `pages::tasks`. Tasks in `@island(name: 'tasks-content', always: true)`; case-insensitive search; status/category/plan/date-range filters; sorts (state/load/latest/date/priority) via fixed hard-coded `when()` branches (no arbitrary input reaches ORDER BY); paginate 6/onEachSide(1); status-themed cards (icons, badges, `mine-card-danger`/`mine-card-secondary` outlines, solid action buttons); `add_plan` deep-link (`#[Url(as: 'add_plan', history: false)]`) prefills and opens the add modal (foreign/unknown ids ignored); edit modal instantly prefilled via deferred `$wire.set(…, false)`.

**Key decisions:** optional selects clear to `null` via `data-value=""` contract (`$wire.set(…, value === '' ? null : value, false)`) — the `mine.select`/`option` components preserve explicit empty-string values, never the label; rate limits `create-task:` / `edit-task:` 5/min and `toggle-task:` 20/min (highest — most frequent), cleared on success, delete not limited; ownership via relationship-scoped `findOrFail` + `TaskPolicy` (`abort_unless`); `#[Locked] $userId` via `HasUser`; task validation: title max 255, description max 5000, `date_format:Y-m-d`, estimated minutes 1–1440, alarm 0–365, priority in low/medium/high; `restrictOnDelete` FKs on `category_id` and `plan_id`; toast + `close-modal` contracts asserted on every mutation.

**Tests:** Livewire page (incl. no-plan edge), action, and access coverage.

### UC-09 – Daily Workload (dashboard)

**Status:** Completed

**Goal:** Show total estimated time per day for the current week with a workload level, fully localized for `fa`.

**Delivered:** `app/Actions/Dashboard/WeeklyWorkloadAction.php`, `app/Enums/WorkloadLevel.php`, `Minutes::format()` (shared, locale-aware), `<x-mine.horizontal-scroll>` (with `todayIndex` centering via `getBoundingClientRect` — LTR/RTL-safe), workload grid on the dashboard.

**Key decisions:** week start is locale-aware — en Sunday, fa Saturday (Jalali, `Carbon::SATURDAY`); grid **includes** completed tasks (total planned effort); one `GROUP BY task_date` query, `#[Computed]`; level thresholds Light ≤120 / Moderate ≤240 / Heavy ≤360 / Very Heavy >360 minutes; localized labels/ranges via `__()` (enum stays language-agnostic); per-level dot/text classes are literal Tailwind classes in a `$workloadMeta` match; "Today" uses the datepicker primary fill, not the green success theme.

**Tests:** action and shared-dashboard Livewire coverage.

### UC-10 – Tasks Needing Attention (dashboard)

**Status:** Completed

**Goal:** Show overdue tasks and tasks whose alarm window started (`task_date - day_before_alarm <= today`), excluding completed, fully localized.

**Delivered:** `app/Actions/Dashboard/AttentionTasksAction.php`, attention list on the dashboard with count badge, overdue/upcoming card themes (`mine-card-danger` / `mine-card-secondary`), localized due/overdue labels, "View task" deep-link to `/tasks` filtered by title, directionally-flipped arrow icon per locale.

**Key decisions:** window math via SQLite `DATE(task_date, '-' || day_before_alarm || ' days')` — per-task modifier, not a fixed window; overdue always included; `day_before_alarm = 0` gates to `task_date = today`; due/overdue labels use `diffInDays()` + `startOfDay()` for consistent boundaries and pluralization.

**Tests:** action, shared-dashboard Livewire, and access coverage.

### UC-11 – Reports

**Status:** Completed

**Goal:** Performance stats (tasks summary, plans summary, per-day workload chart) over a preset or custom date range, fully localized and RTL-compliant.

**Delivered:** `app/Actions/Reports/ReportsAction.php` (`summary()` + `chart()`), `app/Support/Jalali.php` (`weekBounds()`/`monthBounds()`/`format()`), `app/Support/PersianNumber.php` (pure `strtr` digit conversion), shared `Minutes::format()` (deduplicated with UC-09), `<x-mine.bar-chart>` (RTL-aware logical classes, Persian-digit-aware labels), reports page with locale-aware presets.

**Key decisions:** presets in `fa` use Jalali calendar bounds (`Jalali::weekBounds()`/`monthBounds()`), `en` uses Gregorian Sunday-week/month — prevents wrong ranges for Jalali-aware users; plan counting uses date-overlap (`start_date <= end AND finish_date >= start`); completion rate guards division by zero; inverted ranges swapped before querying; chart includes only days that have tasks, ascending; read-only (`#[Computed]`), ownership scoped via `$user->tasks()` / `$user->plans()`.

**Tests:** action, Livewire, and access coverage.
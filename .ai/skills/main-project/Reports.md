# Planner — Audit Reports

Section reports produced for the full project audit, following the structure in `Check-List.md`.
Each finding lists its **status**: ✅ Resolved, 🔸 Deferred (decision), or ⬜ Open.

---

## Section: Overall Project (Global)

Covers global concerns across the whole application: architecture, database schema/migrations, configuration, Vite/frontend tooling, theming, the icon system, localization, and the test setup.

### ✅ Resolved

- **Footer was dead + unlocalized.** Removed entirely (deleted `resources/views/components/mine/footer/index.blade.php` and the commented `{{-- <x-mine.footer /> --}}` usage in `resources/views/layouts/app.blade.php`).
- **Reicon tests were red (4 failing).** Rewrote `tests/Unit/ReiconTest.php` to be icon-name-independent (it derives names from `reicon-icons.json` at runtime), so the suite is green and stays green as icons churn.
- **Pest scaffold leftovers.** Removed unused `toBeOne` expectation and empty `something()` helper from `tests/Pest.php`.

### 🔸 Deferred / By Decision

- **Icon system runtime & usage migration** — mid-refactor; only `EyeOpen`/`EyeClosed` exported and most views still pass the old `variant` prop. User is fixing usages incrementally; leave as-is until then.
- **Pagination vendor template** (`resources/views/vendor/livewire/tailwind.blade.php`) — kept, already tracked in git and safe from `vendor:publish` unless `--force`; icons inside will be migrated with the icon work.
- **"Settings" header/footer items** — kept as placeholders (no settings page yet by design).
- **`UserSeeder` TODO** — deliberate placeholder for admin user after roles.
- **Standardize icon sizing convention** — deferred as part of the icon work.

### ⚪ Missing

- (Overall section had no critical/missing blockers beyond the above after fixes.)

### Overall Status

```text
🟢 Good
```

---

## Section: UC-01 — Login

Goal: *Allow a guest user to sign in with email and password, receive validation or credential errors when needed, and enter the authenticated area after successful login.*

### Current Implementation

Routes (`/login` guest-only → `pages::auth.login`, `/dashboard` auth-only), Livewire page with `$email`/`$password`/`$remember`/`$login_error`, `LoginUserAction` (rate-limited, session regeneration, email normalization, structured logging), `LoginResult` enum, `auth` layout with toast, and 23 tests (11 Livewire + 8 action + 4 access).

### ✅ Resolved

- **Remember-me defaulted ON.** `login.php`: `$remember = true` → `false` (now opt-in).
- **Rate-limit key was `login:{email}|{ip}`** (couldn't stop one IP spraying many emails). Changed to IP-only `login:{ip}` in `LoginUserAction::rateLimitKey()`, matching the guest `register:{ip}` pattern. Tests updated to clear `login:127.0.0.1`.
- **Email validator was looser than Register** — Login used plain `email` while Register used `email:rfc`. Aligned login up to `email:rfc` so both auth forms enforce the same strict rule (applied during the UC-02 pass for cross-UC consistency).

### 🔸 Deferred / By Decision

- **"Forgot password?" link** is a dead `href="#"` — no reset flow exists. Kept for now (product decision); revisit when password reset is in scope.
- **Cooldown countdown text** — left as "Try again in a minute" (does not surface live `available_in`).
- **Autocomplete attributes** on login inputs — skipped (browser inference works).
- **Route-level IP throttle middleware** — skipped; the IP-only action key already provides the anti-spray behavior without a second limiter.

### ✅ Already Strong (no change)

- Brute-force protection (5/min/IP, cleared on success); session-fixation guard (`session()->regenerate()`); email trim+lowercase normalization; generic error (no user enumeration); soft-deleted users blocked; clean `match`-driven `$login_error`; toast survives redirect via flashed session; `guest`/`auth` middleware correct; structured logging; thorough, deterministic tests.

### Critical

- **None.**

### Overall Status

```text
🟢 Good
```

---

## Section: UC-02 — Register

Goal: *Allow a guest user to create a new account with a username, email, and password, then redirect them to login after successful registration.*

### Current Implementation

`/register` (guest-only → `pages::auth.register`, in the same `guest` middleware group as `/` and `/login`), Livewire page with `$username`/`$email`/`$password`/`$password_confirmation`/`$register_error`, `RegisterUserAction` (IP-only rate limit, username/email normalization, uniqueness via DB + integrity-violation race handling, structured logging), `RegisterResult` enum (4 outcomes), `auth` layout with toast, and 28 tests (Livewire UI + 8 action + 2 access). Passwords stored via the `User::password` hashed cast. No auto-auth after registration — the guest is redirected to `/login`.

### ✅ Resolved (in this pass)

- **Dead `mount()` toast re-dispatch removed** (`register.php`). No flow ever redirects to `/register` carrying a flashed toast (the success toast is flashed to `/login`), so the `mount()` re-dispatch never fired. Removed it and its dedicated test. Register now has no `mount()`, keeping it lean.
- **(`email:rfc` consistency)** — no change needed on register itself (it already used `email:rfc`); instead **login** was aligned up to `email:rfc`. See the UC-01 section. Both auth forms now share the same strict email rule.

### 🔸 Deferred / By Decision

- **Both username and email taken → always reports "username taken"** — `RegisterUserAction.php` checks username before email, so a pair colliding on both only surfaces the username error. Kept intentionally: single-error UX, no meaningful ordering win.
- **Duplicate attempts count against the IP bucket** — a valid user who keeps entering an already-taken username/email exhausts the same 5/min IP bucket and is briefly locked out. Kept intentionally: this is deliberate switch-enumeration protection (same design as login); removing it would let attackers probe usernames/emails freely.

### ✅ Already Strong (no change)

- **Rate limiting** — IP-only key `Str::transliterate('register:'.$request->ip())`, 5/min, `RateLimiter::clear()` on success. Consistent with the new `login:{ip}` shape and the recorded `app/Actions` rule.
- **Normalization** — username and email both `Str::lower(Str::trim())` before uniqueness checks.
- **DB unique constraints** on both `username` and `email`; soft-deleted values freed by UC-04's obfuscation.
- **Race-condition handling** — create in a `try`, detect integrity-violation `QueryException` (`SQLSTATE` 23x), disambiguate UsernameTaken vs EmailTaken with `exists()`, rethrow unrelated failures.
- **Result enum** for all outcomes; **structured logging** for success / username-taken / email-taken / rate-limited (with `available_in`).
- **No auto-auth** — user stays a guest and is redirected to `/login` with a flashed success toast (matches AC-02).
- **Guest / auth middleware** correct.
- **Validation** — username regex, `email:rfc`, password `confirmed` + `min:8`, custom localized `messages()`; password fields cleared on failure while username/email are kept.
- **Tests** — Livewire UI (validation, username/email taken incl. mixed-case, rate limit + cooldown retry, clear-error-on-resubmit, success redirect + toast + no-auth) and 8 action tests.

### ⚪ Missing / Out of Scope

- No client-side live uniqueness or debounced pre-submit check (server-side is the source of truth; not required).
- No honeypot/captcha — not in scope for this audit.
- Icons (`leftIcon="User4"` / `"Envelope2"` / `"Lock"`) — **out of scope** (user drives the icon migration, per prior decision).

### Critical

- **None.**

### Overall Status

```text
🟢 Good
```

---

## Section: UC-03 — View and Edit Profile (+ Header)

> **Update pass** (audit complete; agreed fixes applied and verified — see ✅ Resolved below).

Goal: *Allow an authenticated user to view profile information and update editable fields (username, first name, last name, date of birth, country, gender) from a modal.*

### Current Implementation

`/profile` (auth-only → `pages::profile`) in the `auth` middleware group. Uses the `HasUser` trait (`#[Computed] user()`), a `countries()` computed (`cache()->rememberForever` per-locale, ICU-collated for `fa`), `UpdateProfileAction` (auth-scoped rate limit `update-profile:{user_id}|{ip}`, username normalization, race-condition unique handling, nullable blank cleanup, structured logging) with `UpdateProfileResult` enum, an `editProfile()` modal flow with `fillForm()`/`cancelEdit()`, live `profile-updated` event to refresh the header, and a `deleteAccount()` flow (UC-04) reusing the same page. Icons/datepicker are locale-aware, birthday renders Jalali in `fa`. Tests: profile Livewire (23 executions incl. shared delete flow) + access (2) + action (10) + `initials()` unit (4).

The **header** component (`resources/views/components/mine/header/index.blade.php`) is shared across all authenticated pages and the guest home: desktop nav links, mobile nav dropdown, auth user dropdown (profile link, Settings placeholder, logout), and the guest Sign in / Sign up buttons.

### Critical

- **None.**

### 🟢 Good (already correct)

- **Ownership** — `HasUser::user()` scoped to `auth()->id()`; `UpdateProfileAction` also guards with `abort_unless($user->is(auth()->user()), 403)` (defense in depth; frontend-agnostic).
- **Rate limit key** — `update-profile:{user_id}|{ip}` matches the recorded `app/Actions` rule (authenticated action keyed by user ID + IP), 5/min, `clear()` on success.
- **Normalization & nullable handling** — username `Str::lower(Str::trim())`; first/last name trimmed and blank→`null` via `nullableString()` (matches the "stores blank optional fields as null" test).
- **Race-condition uniqueness** — DB unique constraint on `username`; integrity-violation `QueryException` → `UsernameTaken`, unrelated failures rethrow.
- **Validation** — username regex; first/last name `\p{L}` unicode regex (Persian-safe), min/max; gender `Rule::enum`; country `Rule::in(countries keys)`; birthday `before_or_equal:today`. All localized via `messages()`.
- **UX correctness** — modal is `close-by-clicking-away="false"` and `close-by-escaping="false"` (form context); success closes modal + toasts + `profile-updated`; cancel restores state; live header refresh via Alpine listening on `profile-updated.window`.
- **Localization** — countries cached per-locale and ICU-collated for `fa`; country names rendered via `$this->countries[$code]`; birthday formatted Jalali in `fa`, Gregorian otherwise; datepicker position/placement locale-aware.
- **Delete flow (UC-04)** — password confirmed in action with `Hash::check`, rate-limited `delete-account:{user_id}|{ip}`, obfuscates email/username to free unique constraints before soft-delete, logs out + invalidates session, flashes toast, redirects home.
- **Tests** — profile page render/fill, validation (username, text length, gender/country/birthday), full field update, blank→null, username-taken (incl. mixed-case), rate limit + cooldown retry, cancel, and the delete-account flows (wrong password, rate limit, success, retry, error clearing, cancel).

### ✅ Resolved (in this pass)

- **Duplicated `initials()` logic (PHP vs Alpine JS)** — the header's `profile-updated` handler recomputed initials in JS, reimplementing `User::initials()`. Now `profile.php` sends `initials: $this->user->initials()` as part of the `profile-updated` event payload, and the header handler simply assigns `initials = $event.detail.initials` (no JS reimplementation). Single source of truth; the profile test now asserts the `initials` event detail.

### 🔸 Deferred / By Decision

- **Header "Settings" item is inert** — kept placeholder (consistent with Overall/UC-01 records).
- **Email not editable in profile** — intended (matches AC-03); the `profile-updated` event passes the unchanged email for the header.
- **Rate-limit counts username-taken attempts** — intended enumeration protection (consistent with login/register).

### 🟢 Already Strong (no change)

- **Ownership** — `HasUser::user()` scoped to `auth()->id()`; `UpdateProfileAction` also guards with `abort_unless($user->is(auth()->user()), 403)` (defense in depth; frontend-agnostic).
- **Rate limit key** — `update-profile:{user_id}|{ip}` matches the recorded `app/Actions` rule (authenticated action keyed by user ID + IP), 5/min, `clear()` on success.
- **Normalization & nullable handling** — username `Str::lower(Str::trim())`; first/last name trimmed and blank→`null` via `nullableString()`.
- **Race-condition uniqueness** — DB unique constraint on `username`; integrity-violation `QueryException` → `UsernameTaken`, unrelated failures rethrow.
- **Validation** — username regex; first/last name `\p{L}` unicode regex, min/max; gender `Rule::enum`; country `Rule::in(countries keys)`; birthday `before_or_equal:today`. All localized via `messages()`.
- **UX correctness** — modal is `close-by-clicking-away="false"` and `close-by-escaping="false"`; success closes modal + toasts + `profile-updated`; cancel restores state; live header refresh via Alpine listening on `profile-updated.window`.
- **Localization** — countries cached per-locale and ICU-collated for `fa`; country names rendered via `$this->countries[$code]`; birthday formatted Jalali in `fa`, Gregorian otherwise; datepicker position/placement locale-aware.
- **Delete flow (UC-04)** — password confirmed in action with `Hash::check`, rate-limited `delete-account:{user_id}|{ip}`, obfuscates email/username to free unique constraints before soft-delete, logs out + invalidates session, flashes toast, redirects home.
- **Tests** — profile page render/fill, validation, full field update, blank→null, username-taken (incl. mixed-case), rate limit + cooldown retry, cancel, and the delete-account flows.

---

## Section: UC-04 — Delete Account

> **Report pass** (audit only). No code fixes proposed — see status below.

Goal: *Allow an authenticated user to permanently delete their account, requiring password confirmation, with rate limiting to prevent brute-force.*

### Current Implementation

The delete flow shares the profile page (`/profile`, auth-only): a "Delete Account" button opens a confirmation modal with a password field. `deleteAccount()` (in `profile.php`) validates the password field (`required`), calls `DeleteAccountAction` (in `app/Actions/Auth/`), maps `DeleteAccountResult` (`Success` / `WrongPassword` / `RateLimited`) to a localized error, and on success flashes an `Account deleted` toast into the session and `redirectRoute('home', navigate: true)`. The action verifies ownership, rate-limits, checks the password with `Hash::check`, logs the user out, invalidates + regenerates the session, obfuscates email/username to free unique constraints, soft-deletes the user, clears the limiter, and logs. The home page `mount()` pulls the flashed toast and re-dispatches it as a browser toast (correct cross-page toast pattern after a Livewire redirect).

### Critical

- **None.**

### 🟢 Good (already correct)

- **Password confirmed in the action layer**, not the component — `Hash::check($password, $user->password)` — so the guard applies regardless of caller, consistent with `UpdateProfileAction`.
- **Ownership** — `abort_unless($user->is(auth()->user()), 403)`.
- **Rate limit key** — `delete-account:{user_id}|{ip}` matches the recorded `app/Actions` rule (5/min), `clear()` on success. Test keys use `delete-account:{id}|127.0.0.1`.
- **Session hygiene** — `Auth::logout()` + `session()->invalidate()` + `session()->regenerateToken()` prevents session fixation/reuse. The component flashes the toast *after* the action invalidates, so the toast lands in the regenerated session and survives to the home page (verified by test).
- **Credential obfuscation** — email/username rewritten to `deleted-user-{id}` / `deleted_user_{id}` before soft-delete so unique constraints are freed and re-registration with the same email/username is possible.
- **Soft-delete** (`SoftDeletes`) so `withTrashed()` restoration is possible; because every feature query is scoped by `auth()->id()` and the deleted user can never authenticate again, no related data leaks.
- **Structured logging** for all three outcomes (wrong password, rate limited with `seconds_remaining`, success), matching the app's audit-logging convention.
- **Tests** — action tests (success + soft-delete, obfuscation, wrong password, rate limit + time-travel retry, logging) and co-located Livewire UI tests (wrong-password error, rate limit, success with flashed toast + redirect + soft-delete, error clearing, cancel).

### 🔸 Deferred / By Decision

- **"Related data soft-deleted or hard-deleted as configured" (AC step 6)** — currently only the `User` row is soft-deleted; no Tasks/Plans/etc. exist to cascade yet. Scoped queries + unrecoverable session mean nothing leaks. Revisit when UC-07/08 (Plans/Tasks) land and decide cascade cleanup then. Notes-only.

### ⚪ Missing / Out of Scope

- No re-auth beyond the required password-confirmation field; no irreversible-action confirmation phrase — scope-appropriate.

### Overall Status

```text
🟢 Good — no critical, no minor issues; only a deferred "related data" note for when Plans/Tasks exist.
```

---

## Section: UC-05 — Logout

> **Report pass** (audit only). One minor candidate for a fix decision — see below.

Goal: *Allow an authenticated user to end their session and return to the homepage.*

### Current Implementation

`POST /logout` (in the `auth` middleware group) is a closure route that resolves `LogoutUserAction`, calls `execute($request)`, flashes a `Logged out successfully` toast into the session, and returns `204 No Content`. The action does `Auth::logout()` + `session()->invalidate()` + `session()->regenerateToken()` and logs the event. The client is the header's logout dropdown item (a `role="menuitem"` button): it sends a CSRF-token'd `fetch` POST, guards against double-submit with a `busy` flag, then `Livewire.navigate('home')` (fallback `window.location.href`). Home's `mount()` pulls the flashed toast and re-dispatches it as a browser toast (the same cross-page toast pattern as UC-04).

### Critical

- **None.**

### 🟢 Good (already correct)

- **Session hygiene** — `Auth::logout()` + `session()->invalidate()` + `session()->regenerateToken()` (fixation/CSRF-token hygiene), same as the delete flow.
- **Route grouped under `auth`** — a guest hitting `/logout` is redirected to login (tested).
- **CSRF-protected POST from the SPA** with an `X-CSRF-TOKEN` header; no JavaScript falls back to GET.
- **Double-submit guard** (`busy` flag) + `.catch` resets it so a transient failure is retryable without reloading.
- **Toast ordering correct** — toast is flashed after the action regenerates the session, so it survives to home; home `mount()` re-dispatches it (verified by test).
- **Post-logout header state** — after SPA navigation home renders the guest header (Sign in/Sign up); no stale auth UI.
- **Logging** — info log with `user_id` + `ip`, consistent with the audit convention.
- **Tests** — action tests (success/logs out, invalidates session id, regenerates CSRF token, logs) and access tests (auth logout + toast, guest redirect).

### ✅ Resolved (in this pass)

- **Single-case, unused `LogoutResult` enum (YAGNI)** — removed. `LogoutUserAction::execute(Request $request): void` no longer returns a value; `app/Enums/LogoutResult.php` was deleted. The route already discarded the result, and no test or caller consulted the enum, so behavior is unchanged. Unlike `UpdateProfileResult`/`DeleteAccountResult` (multi-state, meaningfully consumed by callers), the logout action cannot fail, so a `void` action is the simplest correct shape.

### ⚪ Missing / Out of Scope

- No error toast on a failed logout request (the fetch `.catch` just resets `busy` so the user can retry) — acceptable, notes-only.
- Icons (`Logout4`, etc.) out of scope per standing decision.

### Overall Status

```text
🟢 Good — no critical; the only minor candidate (one-case LogoutResult enum) was removed.
```

---

## Audit Progress

| Section | Audit | Fixes |
|---|---|---|
| Overall (global) | ✅ done | ✅ done (deferred items by decision) |
| UC-01 Login | ✅ done | ✅ done |
| UC-02 Register | ✅ done | ✅ done (deferred items by decision) |
| UC-03 Profile | ✅ done | ✅ done (deferred items by decision) |
| UC-04 Delete Account | ✅ done (report pass) | ✅ no changes needed |
| UC-05 Logout | ✅ done | ✅ done |
| UC-06 Categories | ⬜ | ⬜ |
| UC-07 Plans | ⬜ | ⬜ |
| UC-08 Tasks | ⬜ | ⬜ |
| UC-09 Daily Workload | ⬜ | ⬜ |
| UC-10 Needing Attention | ⬜ | ⬜ |
| UC-11 Reports | ⬜ | ⬜ |

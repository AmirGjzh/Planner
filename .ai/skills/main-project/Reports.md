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
- **Cooldown countdown text** — left as "Try again in a minute." (does not surface live `available_in`).
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

## Audit Progress

| Section | Audit | Fixes |
|---|---|---|
| Overall (global) | ✅ done | ✅ done (deferred items by decision) |
| UC-01 Login | ✅ done | ✅ done |
| UC-02 Register | ✅ done | ✅ done (deferred items by decision) |
| UC-03 Profile | ⬜ | ⬜ |
| UC-04 Delete Account | ⬜ | ⬜ |
| UC-05 Logout | ⬜ | ⬜ |
| UC-06 Categories | ⬜ | ⬜ |
| UC-07 Plans | ⬜ | ⬜ |
| UC-08 Tasks | ⬜ | ⬜ |
| UC-09 Daily Workload | ⬜ | ⬜ |
| UC-10 Needing Attention | ⬜ | ⬜ |
| UC-11 Reports | ⬜ | ⬜ |

---
paths:
  - 'app/Actions/Auth/**'
  - 'app/Http/Middleware/**'
  - 'app/Notifications/**'
  - 'resources/views/mail/**'
---

# Auth

Audit decisions for the auth actions (login, UC-02 register, UC-05 logout), session freshness, the password-reset notification, and the mail templates.

## Keep the auth forms strict

- Both `login.php` and `register.php` validate email with `['required', 'email:rfc']` — keep them aligned.
- Login and reset cap the password at `max:256` (bcrypt only honors 72 bytes; the bound keeps input sized). Register keeps `confirmed|min:8` with no `max` — its bound is deferred to a UC-02 revisit; until then a >256-char signup can sign in only after a reset shortens the password.

## Register has no toast re-dispatch on mount

No flow redirects to `/register` with a toast; `mount()` re-dispatching one was dead code and was removed. Don't re-add it.

## Enumeration protection on register is deliberate

- When both username and email are already taken, the error always reports "username taken" (checked first).
- Simultaneous duplicate attempts share the same IP-rate-limit bucket.

Both are intentional; keep them.

## Logout returns void

`LogoutUserAction::execute()` returns `void` (no result enum) — a logout can't fail. Don't re-add a single-case result enum.

## Password reset and session-hint contracts

Forgot/reset collapse exists/unknown/broker-throttled into one generic outcome each (anti-enumeration). `forgot-password:{ip}` hits uniformly on every request and never clears — clearing only real sends would leak email existence via limiter timing; `reset-password:{ip}` clears on success. A reset equal to the current password returns `SameAsCurrent`, checked only after `Password::tokenExists` — `Hash::check` must be unreachable without a valid, unexpired token (otherwise the form becomes a password oracle) and the broker must not run, so the token survives for a retry. The 30-day `session_hint` cookie is queued at login success, forgotten at logout/account delete, and consumed once by login's `mount()` for the expired-session toast; it must outlive the 120-minute session because expiry is only detectable from outside the session. Login success regenerates the CSRF token along with the session ID before pulling `url.intended` (fallback `route('dashboard')`); the emailed reset link is rooted at `config('app.url')` by our `resetUrl()` override on `App\Notifications\ResetPasswordNotification` — an extension of the framework's `ResetPassword` rendered by `mail.reset-password` with the recipient's theme variables — never at the request Host, so a forged `Host` header cannot poison the link (`createUrlUsing` stays honored if ever set); the reset page keeps both `token` and `email` as `#[Url]` properties so the form hydrates from the link and survives a refresh. The email is shown in an info alert (`Resetting password for :email`, empty when the link carries no email) — it is link-provided, not user input — and an invalid or missing email maps to the invalid-link alert, never to field validation, since the user cannot fix a value they never typed. `#[Url]` properties hydrate from `request()->query()` **before** the component's `mount()` runs (`SupportAttributes` registers ahead of `SupportLifecycleHooks`), so `mount()` sees both params on a real link. The hint-cookie name is `LoginUserAction::SESSION_HINT`; queue, forget, and consume all reference that constant.

## Password change evicts every older session

- `users.password_changed_at` (nullable) is folded into the original `create_users_table` migration — no separate migration file. `ResetPasswordAction` stamps it alongside the remember-token rotation; there is no other password-update site (profile's password field is delete-account re-auth).
- `ExpireSessionsOnPasswordChange` is appended to the `web` group in `bootstrap/app.php`, so every web request — including the Livewire update route — re-checks freshness (see `.ai/rules/wiring.md`). It stamps `session('auth_time')` on the first authenticated request lacking one (remember-me logins and sessions predating the middleware) and signs out any session whose `auth_time` predates `password_changed_at`: `Auth::logout()` + `session()->invalidate()` + token regenerate, then redirect to login with a flashed toast. Stamping rather than rejecting when `auth_time` is missing is deliberate — a recaller login must not loop-logout, and pre-deploy sessions get one free pass.

## Reset mail is localized and queued

- `User` implements `HasLocalePreference` (`preferredLocale()` returns `users.locale`), so notifications render in the account's saved language even when the guest page's app locale differs.
- The notification is queued (`ResetPasswordNotification implements ShouldQueue`, redis driver) — the worker delivers it, retries per its `--tries`, and records terminal failures in `failed_jobs`.
- `ForgotPasswordAction` wraps `Password::sendResetLink` in `try/catch (Throwable)`: anything that throws while handing the mail off (queue connection down; transport failure on the sync path/tests) is logged as an error and still returns `Sent` — an outage must not break the form or become an existence oracle.

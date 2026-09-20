---
paths:
  - 'app/Actions/Auth/**'
---

# Auth

Audit decisions for the auth actions (UC-02 register / UC-05 logout).

## Keep the auth forms strict

- Both `login.php` and `register.php` validate email with `['required', 'email:rfc']` — keep them aligned.

## Register has no toast re-dispatch on mount

No flow redirects to `/register` with a toast; `mount()` re-dispatching one was dead code and was removed. Don't re-add it.

## Enumeration protection on register is deliberate

- When both username and email are already taken, the error always reports "username taken" (checked first).
- Simultaneous duplicate attempts share the same IP-rate-limit bucket.

Both are intentional; keep them.

## Logout returns void

`LogoutUserAction::execute()` returns `void` (no result enum) — a logout can't fail. Don't re-add a single-case result enum.
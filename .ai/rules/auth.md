---
paths:
  - 'app/Actions/Auth/**'
---

# Auth

## Auth forms email strictness + logout returns void
UC-02/UC-05 audit decisions: (1) both auth forms use `email` => ['required','email:rfc'] (login.php and register.php) — keep them aligned strict. (2) Register's mount() has no toast re-dispatch (no flow redirects to /register with a toast) — don't re-add it. (3) Username+email both taken always reports "username taken" (checked first) and simultaneous duplicate attempts count against the same IP bucket — both deliberate enumeration protection, keep. (4) Logout uses LogoutUserAction which returns void (no result enum) — logouts can't fail, don't re-add a single-case result enum.

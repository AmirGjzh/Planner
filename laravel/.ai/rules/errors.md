---
paths:
  - 'resources/views/errors/**'
---

# Errors

Conventions for the themed error pages under `resources/views/errors/`.

## The handler resolves views exact-first, app-first

- The exception handler looks for `errors::<status>` **exactly**, then the truncated wildcard (`errors::4xx` / `errors::5xx`); the `errors::` namespace maps to `[resources/views/errors, the framework's own error views]` — **our path first**.
- The framework ships exact files for `401 402 403 404 419 429 500 503`, and an exact match always beats our wildcard — so every status we want in our style needs a file **named after it**. Each of those statuses exists as a one-line `@extends('errors::shell')` even though all content lives in the shell; deleting one silently reverts that status to the framework's default page.
- `4xx.blade.php` / `5xx.blade.php` catch everything else (405, 413, 422, 502, …) through the wildcard, so no status ever falls out of the app's look.
- The shell derives the status and the copy map from `$exception` (one of only two variables the handler passes, with `errors`); the status files carry no content of their own. Statuses the map doesn't cover (`401`, `402`) render the generic `Something went wrong` title with no description.

## The shell resolves auth defensively

- **GET dead links run the real pipeline:** `routes/web.php` ends in Laravel's `Route::fallback(fn () => abort(404))`, so an unknown URL matches a route, the web middleware runs (cookies decrypted, session started), and a signed-in visitor's 404 renders their account theme and locale — exactly like every other page. The fallback is GET-only (framework semantics) and partitioned after every real route: it never shadows one, and a POST to a dead URL stays a plain framework 404 instead of tripping CSRF verification.
- Error views can still render outside the pipeline — POST-only 404s, maintenance responses (the maintenance check is global middleware, before the router), and failures whose cause *is* the database or session store. The shell therefore wraps `auth()->user()` in a `try/catch` and falls back to the app defaults — default theme + app locale.
- Locale is set **before** any `__()` runs, and the resolved theme is passed into `x-mine.theme-vars` explicitly (`:theme=...`) so the component never touches `auth()` itself — favicon and `theme-color` follow the same guarded resolution.
- The 500 page renders fixed copy only — **never** `$exception->getMessage()` (it can carry internals); a feature test pins the non-leak.
- No JS, no Livewire, no toast: the shell is a standalone document with a css-only `@vite` — it has to keep rendering when everything else is broken.
- The entrance animation is the **CSS-only twin** of `x-mine.animate` (same `opacity 0→1` + `translateY(16px)`, `0.35s ease-out` with the auth pages' `delay="100"` mirrored as a `100ms both` fill, defined inline in the shell): the Alpine reveal starts hidden and would leave a no-JS page invisible. The `prefers-reduced-motion` neutralization mirrors the mine.css convention.
- The big status number renders through `PersianNumber::convert` in fa (the pagination view's `app()->isLocale('en') ? … : convert(…)` pattern) — `۴۰۴`, not `404`.

## The link is auth-aware

- `Go to home` points at `route('dashboard')` for a resolved user and `route('login')` otherwise — one hop for either audience, and guests are never sent to the (empty) `/` stub.

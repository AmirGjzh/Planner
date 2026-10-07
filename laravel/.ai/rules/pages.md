---
paths:
  - 'resources/views/pages/**'
  - 'resources/views/pages/**/*.test.php'
  - 'app/Livewire/**'
---

# Pages

How full-page Livewire components are built and tested.

## Full pages are Livewire 4 multi-file components

A full page is a `resources/views/pages/⚡<name>/` folder containing:

- `<name>.php` — an anonymous `new class extends Component` file,
- `<name>.blade.php`,
- `<name>.test.php` — a `@pest` page test.

Wire pages to routes with `Route::livewire('/path', 'pages::<name>')`. Do not build class-based pages under `app/Livewire` — that folder holds only shared `Concerns` traits.

## Pages share the user via the `HasUser` trait

Authenticated pages use `App\Livewire\Concerns\HasUser` (a `#[Computed] user()` returning the already-resolved guard user — `auth()->user() ?? abort(403)`, never a second `findOrFail` that re-SELECTs what the guard loaded) instead of resolving the user inline. The trait's `boot()` also calls `app()->setLocale($this->user->locale)`, so every page renders in the user's saved language on every Livewire request (initial + rehydration). Mounted pages pull the session `toast` and `$this->dispatch('toast', ...$toast)`.

Guest pages (home/login/register/forgot-password/reset-password) have no `HasUser` and use the app/.env locale; `login()`/logout/profile-delete set the locale explicitly only at those auth boundaries where no `HasUser` page has rendered yet.

## Toast contract

- Mutations that stay on the page dispatch directly: `$this->dispatch('toast', variant: ..., title: ...)` — `success|info`, 3000ms, bottom-center.
- Flows that redirect flash `session('toast')`; the destination page's `mount()` re-dispatches it. Every page that can receive a flash implements the re-dispatch (login, home, dashboard, verify-email, profile, tasks, plans, categories, reports) — register deliberately has none (see `.ai/rules/auth.md`).
- Pull the flash once in `mount()` and dispatch with `...$toast`.

## Forms fail focus on the first invalid field

Auth forms carry `novalidate` (the browser's native bubbles for `type="email"` are unlocalized and can block submit) and dispatch the focus themselves:

- Wrap `$this->validate()` in `try { ... } catch (ValidationException) { $this->dispatch('focus-first-error'); throw; }` — dispatch before rethrowing so the event ships with the same request.
- The form binds the listener itself: `<form x-data x-on:focus-first-error.window>` runs `$nextTick(() => $el.querySelector('[aria-invalid="true"]')?.focus())`, so the first invalid field inside that form receives focus.
- Never rely on the browser's own focus for invalid fields on these pages.

## Auth form rhythm and overflow guards

Auth form rhythm: fields are separated by the form's `gap-4`. Auth headings are `text-xl tracking-tight`; links are `text-sm font-medium` except inside alerts, where an inline link stays `font-semibold` for visibility (deliberate — the app ships Regular/Medium/SemiBold only, never `font-bold`).

The card footer is always the same prompt + link row, `mt-5` below the form (or the last block) — auth cards never render an `OR` separator.

Variable user-supplied text always ships with an overflow guard — an email is an unbreakable token that overflows a card instead of wrapping. At the tail of a line, put `truncate` on the paragraph; mid-sentence, interpolate it as `<span class="inline-block max-w-full truncate align-bottom">` via `{!! __('…') !!}` with `e($email)` (the translation string is ours; the value is escaped) and pin both the classes and the escaping in a test.

## Page titles compose in the layout, from a plain string

- Pages pass a title as a plain string via `#[Layout('layouts::auth', ['title' => 'Sign in'])]` — layout args must be PHP constant expressions, so no `__()`/`route()` calls in the attribute.
- The layout renders `{{ ($title ?? null) ? __($title).' — '.config('app.name') : config('app.name') }}` and translates at render time, so the title follows the page locale.
- Both layouts (`layouts::app`, `layouts::auth`) own the `— {{ config('app.name') }}` suffix; pages must not repeat the app name.

## Co-locate page tests next to pages, ref `pages::<name>`

- Live at `resources/views/pages/<page>/*.test.php` (e.g. `⚡dashboard/dashboard.test.php`).
- Picked up by phpunit.xml's `Components` testsuite (`suffix=".test.php"` over `resources/views`) and by `Pest.php` `->in(..., '../resources/views')`; global namespace (not `Tests\`), with `LazilyRefreshDatabase` from `Pest.php`.
- Drive them with `Livewire::actingAs($user)->test('pages::<name>')->assertSee(...)->assertSet(...)->assertDispatched(...)`.
- Nested auth pages use the dotted ref `pages::auth.login`.

## Co-located per-file helper functions build test records

Each page test file declares its own top-level helpers at the top of the file (`dashboardTask()`, `dashboardWeek()`, `reportTask()`, `attentionTask()`, `workloadTask()`, `taskDate()`/`makeTask()`, `planWideRange()`, `profileUser()`, ...). Helpers:

- create via a relationship (`$user->tasks()->create(...)`),
- reuse a `firstOrCreate` "Work" category,
- return `now()`-relative dates.

Follow this co-located-helper pattern instead of shared fixtures/seeders.

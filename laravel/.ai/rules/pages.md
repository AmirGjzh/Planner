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

Authenticated pages use `App\Livewire\Concerns\HasUser` (a `#[Computed] user()` returning `User::findOrFail(auth()->id())`) instead of resolving the user inline. The trait's `boot()` also calls `app()->setLocale($this->user->locale)`, so every page renders in the user's saved language on every Livewire request (initial + rehydration). Mounted pages pull the session `toast` and `$this->dispatch('toast', ...$toast)`.

Guest pages (home/login/register) have no `HasUser` and use the app/.env locale; `login()`/logout/profile-delete set the locale explicitly only at those auth boundaries where no `HasUser` page has rendered yet.

## Toast contract

- Mutations that stay on the page dispatch directly: `$this->dispatch('toast', variant: ..., title: ...)` — `success|info`, 3000ms, bottom-center.
- Flows that redirect flash `session('toast')`; the destination page's `mount()` re-dispatches it (login, home, profile). Only pages expected to receive a flash implement the re-dispatch — register deliberately has none (see `.ai/rules/auth.md`).
- Pull the flash once in `mount()` and dispatch with `...$toast`.

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
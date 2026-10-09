---
paths:
  - 'tests/**'
  - 'tests/**/*.test.php'
  - 'database/factories/**'
---

# Tests

How tests are written (Pest): what to pin, how to name, the Persian-behavior rules, and the opt-in browser tier.

## Write tests with Pest `it()` + `expect()`, LazilyRefreshDatabase

- All tests are Pest `it('desc', function () {})` closures using the `expect()` expectation API — no PHPUnit classes.
- DB reset is centrally wired in `tests/Pest.php` via `pest()->use(LazilyRefreshDatabase::class)->in('Feature', 'Unit', 'Browser', '../resources/views')` — test files declare no trait.
- Use factories for the principal subject user, and build owned records via relationship `->create()` (never real fixture/seed calls).
- Actions are tested by real integration: `app(XxxAction::class)->execute(...)` against the in-memory SQLite DB.

## Test Persian behavior via the factory locale; inline `setLocale` only for bare Action / render tests

- **Livewire page-component tests:** set the fa locale through the user, not inline — `$user = User::factory()->create(['locale' => 'fa'])`, and let `HasUser::boot()` apply it. Do **not** call `app()->setLocale('fa')` at the top of a page-test closure: the factory defaults `locale='en'` for determinism and the page reads the locale from the user.
- **Bare tests** (no Livewire boot): pure Action tests exercising a locale-aware action/helper (e.g. `WeeklyWorkloadActionTest`) and view/notification renders (e.g. `resources/views/mail/reset-password.test.php`) take inline `app()->setLocale('fa')` at the top of the `it(...)` closure — correct and must stay; never in `beforeEach`, which is reserved for `RateLimiter::clear`.
- Assert Jalali output via `App\Support\Jalali::format(...)` and Persian literals.
- Week base changes by locale: `now()->startOfWeek(Carbon::SUNDAY)` for en, `Carbon::SATURDAY` for fa.
- Build dates relative to `now()` (`subDays`/`addDays`/`startOfWeek`/`yesterday`); avoid hardcoded calendar dates.

## Head content needs an HTTP test, not `Livewire::test`

`Livewire::test()` renders only the component view, never the layout's `<head>` — title, font preload, and other head assertions must go through a feature HTTP test (`$this->get('/login')->assertSee('rel="preload"', false)`), e.g. in `tests/Feature/Auth/`.

## Tests pin behavior and contracts, not markup styling

- Do **not** assert design/utility classes — heading type scale (`text-xl`, `tracking-tight`), spacing (`gap-4`, `p-6`), animation duration (`transitionDuration = '350ms'`), or shared component entrance classes. Those are reviewed visually, not behavior contracts; a test pinning them turns every styling tweak into a failing test.
- **Do** assert behavior: validation outcomes and their user-visible copy, dispatched events, `aria-*` wiring that gates behavior, escaping of interpolated user/URL input, overflow-guard classes (pages.md mandates those), rendered user-visible copy, and rate-limit/security contracts (`attempts('{prefix}:{ip}')`, clear-on-success, rotation).
- **Assert the contract, not the harness's exception plumbing.** Ownership is proven by the other user's record staying unchanged and no success toast being dispatched — never `expectException(ModelNotFoundException)`: whether that exception escapes `Livewire::test()->call()` is Livewire's internal choice (4.4.7 renders it as a 404), so such an assertion pins framework internals and breaks on dependency bumps.
- One defect or one contract per test; when a test is fully subsumed by a stronger twin at the same layer, delete it instead of keeping both (cross-layer twins stay — page proves the UI mapping, action proves the domain outcome).

## Name tests after the observable contract

- A test's name must promise only what it can observe: `Livewire::test` never runs JavaScript, so "focuses the first invalid field" is false advertising — name the observable event instead: `dispatches focus-first-error when validation fails`. Likewise `renders ... type scale` for markup pins becomes nothing at all (delete), while `renders the email field with semantic input attributes` stays because those attributes are the contract.

## Browser tier: opt-in, real-JS contracts only

- `tests/Browser/` is a **separate, opt-in tier** — it is deliberately **not** listed in `phpunit.xml`'s `<testsuite>` entries, so plain `composer test` never starts a browser. Run it with `composer test:browser` (narrow it with `-- --filter=SignInJourneyTest` or `-- --filter="auto-dismisses"`; setup = the one-time Chromium install in README step 8).
- **What belongs there:** contracts that need real JavaScript and real navigation — DOM focus, Alpine toggles (password eye), toast auto-dismiss timing, toast persistence across navigations/reloads (including the hover-paused deadline), redirect journeys across pages, axe via `assertNoAccessibilityIssuesExceptContrast` (every axe rule except `color-contrast`, which stays off while the theme palettes are frozen — see `tests/Pest.php`). Everything assertable from `Livewire::test` or an HTTP feature test stays in the fast tier — never move a logic assertion into the browser tier for convenience.
- **Selector discipline:** prefer semantic/text selectors (`getByText`, `getByRole`-style copy from the page tests); the input component gives every field a stable `id`/`name` from `wire:model`, so `#field` / `[name=field]` beats brittle CSS chains. Assert user-visible copy (translated strings), never utility classes (see the rule above). A bare word that matches no text (e.g. `keys('body', 'Tab')`) falls back to a text lookup that **never resolves — no timeout fires and the whole run hangs**; press from the document root with explicit CSS instead (`keys(':root', 'Tab')`).
- **Flake guards:** `assertSee`/`assertDontSee` are **single-shot snapshots** — `waitForText` is only an alias of `assertSee`, never a retry — so `wait(1)` after anything that renders (a navigation, a `script()` dispatch) *before* asserting text. Pages also animate in (`animate-fade-in`): settle with `$pages->wait(1.5)` before layout-wide assertions (never read a layout mid-animation), and `wait(7)` to observe a 6s toast auto-dismiss. Rate-limit keys are IP-based (`login:127.0.0.1`, …) and shared with the in-process server — clear them in `beforeEach`.
- **Debugging:** a failed assertion auto-screenshots into `tests/Browser/Screenshots/` (git-ignored); `--debug` pauses interactively (works headless); `--headed` needs an X display and is unavailable in the container. `Route::block` locks are released in `finally`, so sequential browser requests are safe.
- **Pins stay in lockstep:** the exact `playwright` version in `package.json` and in the dev Dockerfile's `playwright@X` must move together (bump both + rebuild + reinstall Chromium — details in `docker/development.md`).

---
paths:
  - 'tests/**'
  - 'tests/**/*.test.php'
---

# Tests

How tests are written (Pest), including the Persian-behavior rules.

## Write tests with Pest `it()` + `expect()`, LazilyRefreshDatabase

- All tests are Pest `it('desc', function () {})` closures using the `expect()` expectation API (181 `it()`, zero PHPUnit classes).
- DB reset is centrally wired in `tests/Pest.php` via `pest()->use(LazilyRefreshDatabase::class)->in('Feature', 'Unit', '../resources/views')` — test files declare no trait.
- Use factories for the principal subject user, and build owned records via relationship `->create()` (never real fixture/seed calls).
- Actions are tested by real integration: `app(XxxAction::class)->execute(...)` against the in-memory SQLite DB.

## Test Persian behavior via the factory locale; inline `setLocale` only for bare Action tests

- **Livewire page-component tests:** set the fa locale through the user, not inline — `$user = User::factory()->create(['locale' => 'fa'])`, and let `HasUser::boot()` apply it. Do **not** call `app()->setLocale('fa')` at the top of a page-test closure: the factory defaults `locale='en'` for determinism and the page reads the locale from the user.
- **Pure Action tests** (no Livewire boot, e.g. `WeeklyWorkloadActionTest`) that exercise a locale-aware action/helper directly: inline `app()->setLocale('fa')` at the top of the `it(...)` closure is correct and must stay — never in `beforeEach`, which is reserved for `RateLimiter::clear`.
- Assert Jalali output via `App\Support\Jalali::format(...)` and Persian literals.
- Week base changes by locale: `now()->startOfWeek(Carbon::SUNDAY)` for en, `Carbon::SATURDAY` for fa.
- Build dates relative to `now()` (`subDays`/`addDays`/`startOfWeek`/`yesterday`); avoid hardcoded calendar dates.
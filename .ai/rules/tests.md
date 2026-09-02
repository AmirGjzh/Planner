---
paths:
  - 'tests/**'
  - 'tests/**/*.test.php'
---

# Tests

## Write tests with Pest it()+expect(), LazilyRefreshDatabase
All tests are Pest `it('desc', function () {})` closures using the `expect()->toBe()` expectation API (181 `it()`, zero PHPUnit classes). DB reset is centrally wired in tests/Pest.php via `pest()->use(LazilyRefreshDatabase::class)->in('Feature','Unit','../resources/views')`, so test files declare no trait. Use factories for the principal subject user, and build owned records via relationship `->create()` (never real fixture/seed calls). Actions are tested by real integration: `app(XxxAction::class)->execute(...)` against the sqlite in-memory DB.

## Test Persian behavior with inline app()->setLocale('fa')
To test fa locale, call `app()->setLocale('fa');` at the top of the specific `it(...)` closure (never in beforeEach — beforeEach is reserved for `RateLimiter::clear`). Assert Jalali output via `App\Support\Jalali::format(...)` and Persian literals. Week base changes by locale: `now()->startOfWeek(Carbon::SUNDAY)` for en, `Carbon::SATURDAY` for fa. Build dates relative to now() (subDays/addDays/startOfWeek/yesterday); avoid hardcoded calendar dates.

---
paths:
  - 'tests/**'
  - 'tests/**/*.test.php'
---

# Tests

## Write tests with Pest it()+expect(), LazilyRefreshDatabase
All tests are Pest `it('desc', function () {})` closures using the `expect()->toBe()` expectation API (181 `it()`, zero PHPUnit classes). DB reset is centrally wired in tests/Pest.php via `pest()->use(LazilyRefreshDatabase::class)->in('Feature','Unit','../resources/views')`, so test files declare no trait. Use factories for the principal subject user, and build owned records via relationship `->create()` (never real fixture/seed calls). Actions are tested by real integration: `app(XxxAction::class)->execute(...)` against the sqlite in-memory DB.

## Test Persian behavior via the factory locale; inline setLocale only for bare Action tests
For Livewire page-component tests, set fa locale through the user, not inline: `$user = User::factory()->create(['locale' => 'fa'])` and let `HasUser::boot()` apply it (`app()->setLocale($this->user->locale)`), because boot() runs on both initial render and rehydration. Do NOT call `app()->setLocale('fa')` at the top of a page-test closure — the factory defaults `locale='en'` for determinism and the page reads the locale from the user. The exception is pure Action tests with no Livewire boot (e.g. `WeeklyWorkloadActionTest`) that exercise a locale-aware action/helper directly — there inline `app()->setLocale('fa')` at the top of the `it(...)` closure is correct and must stay (never in beforeEach, which is reserved for `RateLimiter::clear`). Assert Jalali output via `App\Support\Jalali::format(...)` and Persian literals. Week base changes by locale: `now()->startOfWeek(Carbon::SUNDAY)` for en, `Carbon::SATURDAY` for fa. Build dates relative to now() (subDays/addDays/startOfWeek/yesterday); avoid hardcoded calendar dates.

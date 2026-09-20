---
paths:
  - 'tests/Feature/Actions/**'
---

# Feature Actions

How Action feature tests are organized and written.

## Mirror app/Actions under tests/Feature/Actions

Each Action gets a feature test at `tests/Feature/Actions/<Domain>/<XxxAction>Test.php`, mirroring the `app/Actions/<Domain>` tree.

- Exercise the real Action: `app(XxxAction::class)->execute(...)` — never mock the Action or its collaborators.
- Assert side-effect logging with `Log::spy()` + `Mockery::on(fn ($ctx) => ...)` + `shouldHaveReceived`.
- Clear rate-limit buckets in `beforeEach` (`RateLimiter::clear('prefix:key')`) for rate-limited actions.
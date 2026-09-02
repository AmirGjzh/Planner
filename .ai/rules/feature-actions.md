---
paths:
  - 'tests/Feature/Actions/**'
---

# Feature Actions

## Action tests co-located under tests/Feature/Actions mirroring app/Actions
Each Action class gets a feature test at tests/Feature/Actions/<Domain>/<XxxAction>Test.php mirroring app/Actions/<Domain>. They exercise the real Action via `app(XxxAction::class)->execute(...)` (no mocking of the Action or its collaborators). Side-effect logging is asserted with `Log::spy()` + `Mockery::on(fn ($ctx) => ...)` `shouldHaveReceived`. Rate-limited actions clear `RateLimiter::clear('prefix:key')` in a `beforeEach`.

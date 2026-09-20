---
paths:
  - 'app/Livewire/**'
  - 'resources/views/pages/**'
  - 'database/factories/**'
---

# Factories

Rules for user locale in Livewire pages and factories.

## Apply per-user locale in `HasUser::boot()`, not at call sites

Per-user language is applied via the shared `HasUser` trait in `boot()`:

```php
app()->setLocale($this->user->locale);
```

- `boot()` runs on every Livewire request (initial render **and** rehydration), so `wire:model.live`/action updates keep the user's saved locale. `mount()` does **not** run on rehydration and must not be the only place.
- Guest pages (home/login/register) have no `HasUser` and use the app/.env locale — that's why `login()`/logout/profile-delete call `app()->setLocale(...)` explicitly, only at those auth boundaries.
- `UserFactory` defaults `locale = 'en'` for deterministic tests. fa page tests must create the user explicitly:
  - `User::factory()->create(['locale' => 'fa'])` — rely on `boot()`, never on a bare factory's (now fixed `en`) locale and never on inline `app()->setLocale('fa')` for a Livewire page test.
- Pure Action tests (no Livewire boot, e.g. `WeeklyWorkloadActionTest`) still use inline `app()->setLocale('fa')` — see `.ai/rules/tests.md`.
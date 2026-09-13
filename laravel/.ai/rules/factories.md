---
paths:
  - 'app/Livewire/**'
  - 'resources/views/pages/**'
  - 'database/factories/**'
---

# Factories

## Apply per-user locale in HasUser::boot(), not setLocale at call sites
Per-user language is applied via the shared `HasUser` trait `boot()`: `app()->setLocale($this->user->locale)`. `boot()` runs on every Livewire request (initial render AND rehydration), so `wire:model.live`/action updates keep the user's saved locale — `mount()` does NOT run on rehydration and must not be the only place. Guest pages (home/login/register) have no `HasUser` and use the app/.env locale; that's why `login()`/logout route/profile delete explicitly call `app()->setLocale(...)` only at those auth boundaries. UserFactory defaults `locale='en'` for deterministic tests, so fa page tests must create the user explicitly: `User::factory()->create(['locale' => 'fa'])` and rely on boot() — never on a bare factory's (now fixed `en`) locale and never on inline `app()->setLocale('fa')` for a Livewire page test. Pure Action tests (no Livewire boot, e.g. `WeeklyWorkloadActionTest`) still use inline `app()->setLocale('fa')` — see `.ai/rules/tests.md`.

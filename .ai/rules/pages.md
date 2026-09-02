---
paths:
  - 'resources/views/pages/**'
  - 'resources/views/pages/**/*.test.php'
---

# Pages

## Full pages use Livewire 4 multi-file components
Full pages are Livewire multi-file components: a `resources/views/pages/⚡<name>/` folder with co-located `<name>.php` (an anonymous `new class extends Component`) and `<name>.blade.php`, plus a `@pest` `<name>.test.php`. Wire them to routes with `Route::livewire('/path', 'pages::<name>')`. Do not build class-based pages under `app/Livewire` (that folder holds only shared Concerns traits).

## Pages share the user via the HasUser trait
Authenticated pages use the `App\Livewire\Concerns\HasUser` trait (a `#[Computed] user()` returning `User::findOrFail(auth()->id())`) instead of resolving the user inline. Mounted pages pull the session `toast` and `$this->dispatch('toast', ...$toast)`.

## Co-locate page tests next to pages, ref 'pages::<name>'
Page tests live in the page component's own folder as resources/views/pages/<page>/*.test.php (e.g. ?dashboard/dashboard.test.php). They are picked up by phpunit.xml's 'Components' testsuite (`<directory suffix=".test.php">resources/views</directory>`) and by Pest.php `->in(..., '../resources/views')`; they are global-namespace (NOT Tests\) and get LazilyRefreshDatabase from Pest.php. Drive them with `Livewire::actingAs($user)->test('pages::<name>')->assertSee(...)->assertSet(...)->assertDispatched(...)`; nested auth pages use the dotted ref 'pages::auth.login'.

## Co-located per-file helper functions build test records
Each page test file declares its own top-level helper function(s) at the top of the file to build owned records, e.g. dashboardTask()/dashboardWeek(), reportTask()/reportPlan(), attentionTask(), workloadTask(), taskDate()/makeTask(), planWideRange()/planInCurrentMonth(), profileUser(). Helpers create via relationship (`$user->tasks()->create(...)`), reuse a firstOrCreate 'Work' category, and return now()-relative dates. Follow this co-located-helper pattern instead of shared fixtures/seeders.

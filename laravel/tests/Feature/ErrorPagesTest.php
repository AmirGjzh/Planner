<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

it('renders our themed 404 page for unknown URLs', function () {
    $default = config('themes.default');

    $this->get('/this-page-does-not-exist')
        ->assertStatus(404)
        ->assertSee('Page not found')
        ->assertSee('The page you are looking for does not exist or may have been moved.')
        ->assertSee('href="'.asset("favicons/{$default}.svg").'"', false)
        ->assertSee('<meta name="theme-color" content="'.config('themes.vars')[$default]['mine-page-bg'].'"', false);
});

it('renders the error page in the account theme when a signed-in user hits an error', function () {
    $default = config('themes.default');
    $theme = collect(config('themes.name'))->first(fn (string $slug) => $slug !== $default);

    Route::get('/__error-theme-test', fn () => abort(404));

    $user = User::factory()->create(['theme' => $theme]);

    $this->actingAs($user)
        ->get('/__error-theme-test')
        ->assertStatus(404)
        ->assertSee('href="'.asset("favicons/{$theme}.svg").'"', false)
        ->assertSee('<meta name="theme-color" content="'.config('themes.vars')[$theme]['mine-page-bg'].'"', false)
        ->assertDontSee('href="'.asset("favicons/{$default}.svg").'"', false);
});

it('renders our themed copy for every covered status and for wildcard fallbacks', function (int $status, string $title) {
    Route::get('/__error-status-test', fn () => abort($status));

    $this->get('/__error-status-test')
        ->assertStatus($status)
        ->assertSee($title);
})->with([
    '403' => [403, 'You do not have access to this page'],
    '404' => [404, 'Page not found'],
    '419' => [419, 'Page expired'],
    '429' => [429, 'Too many requests'],
    '500' => [500, 'Something went wrong'],
    '503' => [503, 'Under maintenance'],
    'uncovered 4xx falls back to the generic copy' => [405, 'Something went wrong'],
]);

it('never leaks the exception message on a server error', function () {
    config(['app.debug' => false]);

    Route::get('/__error-boom-test', fn () => throw new RuntimeException('sensitive connection details'));

    $this->get('/__error-boom-test')
        ->assertStatus(500)
        ->assertSee('Something went wrong')
        ->assertDontSee('sensitive connection details');
});

it('renders the error page in the account language for a signed-in fa user', function () {
    Route::get('/__error-locale-test', fn () => abort(404));

    $user = User::factory()->create(['locale' => 'fa']);

    $this->actingAs($user)
        ->get('/__error-locale-test')
        ->assertStatus(404)
        ->assertSee('صفحه پیدا نشد')
        ->assertSee('۴۰۴');
});

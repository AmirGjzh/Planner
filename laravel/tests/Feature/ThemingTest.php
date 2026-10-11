<?php

use App\Models\User;

/**
 * The first theme that is not the app default — the odd one out every
 * theme-marker assertion needs to prove the right file/chrome was served.
 */
function nonDefaultTheme(): string
{
    return collect(config('themes.name'))->first(fn (string $slug) => $slug !== config('themes.default'));
}

it('serves guest auth pages with the default theme favicon and browser chrome', function () {
    $default = config('themes.default');

    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('href="'.asset("favicons/{$default}.svg").'"', false)
        ->assertSee('<meta name="theme-color" content="'.config('themes.vars')[$default]['mine-page-bg'].'"', false);
});

it('serves the app shell with the user theme favicon and browser chrome', function () {
    $theme = nonDefaultTheme();

    $user = User::factory()->create(['theme' => $theme]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('href="'.asset("favicons/{$theme}.svg").'"', false)
        ->assertSee('<meta name="theme-color" content="'.config('themes.vars')[$theme]['mine-page-bg'].'"', false);
});

it('pins auth surfaces to the default theme even for a user with a saved theme', function () {
    $default = config('themes.default');
    $theme = nonDefaultTheme();

    $user = User::factory()->unverified()->create(['theme' => $theme]);

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertSuccessful()
        ->assertSee('href="'.asset("favicons/{$default}.svg").'"', false)
        ->assertDontSee('href="'.asset("favicons/{$theme}.svg").'"', false);
});

it('chains the SPA progress bar color to the active theme variable', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('--livewire-progress-bar-color: var(--mine-btn-primary-bg, #173B67);', false);
});

it('ships a favicon file for every configured theme', function () {
    $missing = collect(config('themes.vars'))
        ->keys()
        ->reject(fn (string $slug) => is_file(public_path("favicons/{$slug}.svg")))
        ->all();

    expect($missing)->toBeEmpty();
});

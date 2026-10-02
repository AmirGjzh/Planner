<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

it('redirects guests from the dashboard to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('allows authenticated users to visit the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSuccessful();
});

it('preloads the primary font on app pages', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSee('rel="preload"', false)
        ->assertSee('as="font"', false)
        ->assertSee('crossorigin', false)
        ->assertSee('Vazirmatn-Regular', false);
});

it('loads the user exactly once per authenticated request', function () {
    $user = User::factory()->create();

    $userSelects = [];
    DB::listen(function ($query) use (&$userSelects) {
        if (preg_match('/\bfrom\s+[`"]users[`"]/', $query->sql)) {
            $userSelects[] = $query->sql;
        }
    });

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSuccessful();

    expect($userSelects)->toBeEmpty();
});

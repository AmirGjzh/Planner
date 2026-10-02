<?php

use App\Models\User;

it('renders login for guests without database queries', function () {
    $this->expectsDatabaseQueryCount(0);

    $this->get('/login')->assertSuccessful();
});

it('gives the login page a unique title', function () {
    $this->get('/login')
        ->assertSee('<title>'.__('Sign in').' — '.config('app.name').'</title>', false);
});

it('preloads the primary font on guest pages', function () {
    $this->get('/login')
        ->assertSee('rel="preload"', false)
        ->assertSee('as="font"', false)
        ->assertSee('crossorigin', false)
        ->assertSee('Vazirmatn-Regular', false);
});

it('redirects authenticated users away from login page', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect('/dashboard');
});

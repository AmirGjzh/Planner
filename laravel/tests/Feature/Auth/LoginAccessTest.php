<?php

use App\Models\User;

it('allows guests to visit login page', function () {
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

it('preloads the primary font on app pages', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/dashboard')
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

it('redirects guests from dashboard to login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('allows authenticated users to visit dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSuccessful();
});

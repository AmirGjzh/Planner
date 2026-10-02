<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
});

it('signs in, shows the success toast, then auto-dismisses it', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Remember me')
        ->assertScript('document.querySelector("input[type=checkbox]").checked', true)
        ->click('Sign in')
        ->assertPathIs('/dashboard')
        ->assertSee('You\'re signed in');

    $this->assertAuthenticatedAs($user);

    $page->wait(7)
        ->assertDontSee('You\'re signed in');
});

it('returns to the originally requested page after signing in', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    visit('/dashboard')
        ->assertPathIs('/login')
        ->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $this->assertAuthenticatedAs($user);
});

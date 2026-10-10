<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

it('signs out through the header menu', function () {
    RateLimiter::clear('login:127.0.0.1');

    User::factory()->create([
        'username' => 'amir',
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Log out')
        ->assertPathIs('/')
        ->assertSee('Logged out successfully')
        // The AC: the guest navigation is back ("Login and Register options").
        ->assertSee('Sign in')
        ->assertSee('Sign up');

    $this->assertGuest();
});

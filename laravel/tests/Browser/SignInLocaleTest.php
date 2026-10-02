<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

it('shows the sign-in toast in the user language', function () {
    RateLimiter::clear('login:127.0.0.1');

    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
        'locale' => 'fa',
    ]);

    visit('/login')
        ->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard')
        ->assertSee('وارد شدی');
});

<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('register:127.0.0.1');
});

it('creates an account and returns to sign in', function () {
    visit('/register')
        ->fill('username', 'amir_user')
        ->fill('email', 'amir@example.com')
        ->fill('password', 'password123')
        ->fill('password_confirmation', 'password123')
        ->click('Sign up')
        ->assertPathIs('/login')
        ->assertSee('Your account is ready, sign in to continue');

    expect(User::firstWhere('email', 'amir@example.com'))->not->toBeNull();
    $this->assertGuest();
});

it('keeps the form when the password confirmation does not match', function () {
    visit('/register')
        ->fill('username', 'amir_user')
        ->fill('email', 'amir@example.com')
        ->fill('password', 'password123')
        ->fill('password_confirmation', 'different')
        ->click('Sign up')
        ->assertSee('The passwords don\'t match.')
        ->assertPathIs('/register');

    expect(User::firstWhere('email', 'amir@example.com'))->toBeNull();
    $this->assertGuest();
});

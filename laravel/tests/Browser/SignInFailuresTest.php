<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
});

it('stays on the sign-in page with an error for wrong credentials', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    visit('/login')
        ->fill('email', 'amir@example.com')
        ->fill('password', 'wrong-password')
        ->click('Sign in')
        ->assertPathIs('/login')
        ->assertSee('Check your email and password, then try again.')
        ->assertValue('#password', '');

    $this->assertGuest();
});

it('validates the email format in the real browser', function () {
    visit('/login')
        ->fill('email', 'bad-email')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertSee('Enter a valid email.')
        ->assertAttribute('#email', 'aria-invalid', 'true');

    $this->assertGuest();
});

it('focuses the first invalid field on an empty submit', function () {
    visit('/login')
        ->click('Sign in')
        ->assertAttribute('#email', 'aria-invalid', 'true')
        ->assertScript('document.activeElement.id', 'email');

    $this->assertGuest();
});

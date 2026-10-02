<?php

use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('forgot-password:127.0.0.1');
});

it('shows the sent state and restarts back to the form', function () {
    visit('/forgot-password')
        ->fill('email', 'someone@example.com')
        ->click('Send reset link')
        ->assertSee('Check your email')
        ->assertSee('a reset link has been sent')
        ->click('Use a different email')
        ->assertSee('Send reset link')
        ->assertSee('Enter your email to receive a reset link');
});

it('shows the same sent state for an unknown email', function () {
    visit('/forgot-password')
        ->fill('email', 'nobody@example.com')
        ->click('Send reset link')
        ->assertSee('Check your email')
        ->assertSee('a reset link has been sent');

    $this->assertGuest();
});

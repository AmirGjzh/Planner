<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
});

it('signs an unverified account in and lands on an accessible verification notice', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/email/verify')
        ->assertSee('Verify your email address');

    $this->assertAuthenticatedAs($user);

    // Let the card entrance animation settle before the layout-wide assertions.
    $page->wait(1.5)
        ->assertNoSmoke();

    assertNoAccessibilityIssuesExceptContrast($page);
});

it('resends a verification email and shows the success toast', function () {
    User::factory()->unverified()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    visit('/login')
        ->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/email/verify')
        ->click('Resend verification email')
        ->wait(1)
        ->assertSee('Verification email sent');
});

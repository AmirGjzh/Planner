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

it('signs out from the verification notice and lands home signed out', function () {
    User::factory()->unverified()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/email/verify')
        ->click('Log out')
        ->wait(1)
        ->assertPathIs('/')
        ->assertSee('Logged out successfully');

    $this->assertGuest();
});

it('keeps the user on the notice and shows a danger toast when logout fails', function () {
    User::factory()->unverified()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/email/verify');

    // Kill the fetch so the non-ok branch runs: the session is still alive,
    // so the page must stay put (auth would redirect a dead one) and say so.
    // The trailing undefined keeps Playwright from auto-invoking the stub.
    $page->script('window.fetch = () => Promise.reject(new Error("stubbed")); undefined');

    $page->click('Log out')
        ->wait(1)
        ->assertPathIs('/email/verify')
        ->assertSee('Log out failed');
});

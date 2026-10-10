<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->user = User::factory()->create([
        'username' => 'amir',
        'email' => 'amir@example.com',
        'password' => 'current-password',
    ]);

    RateLimiter::clear('login:127.0.0.1');
});

it('deletes the account through the confirmation modal and lands home signed out', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Profile')
        ->assertPathIs('/profile');

    // The danger box comes first in DOM order, so the trigger wins over the
    // modal's own submit button for the shared 'Delete account' text.
    $page->click('Delete account')
        ->wait(1)
        ->assertSee('This action is permanent, enter your password to continue.');

    // The open dialog must carry an accessible name (shared labelledBy prop).
    assertNoAccessibilityIssuesExceptContrast($page);

    $page->fill('password', 'wrong-password')
        ->keys('password', 'Enter')
        ->wait(1)
        ->assertSee('Wrong password!');

    $page->fill('password', 'current-password')
        ->keys('password', 'Enter')
        ->wait(1)
        ->assertPathIs('/')
        ->assertSee('Account deleted');

    $this->assertGuest();
});

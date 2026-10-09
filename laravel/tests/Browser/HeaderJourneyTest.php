<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->user = User::factory()->create([
        'username' => 'amir',
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    RateLimiter::clear('login:127.0.0.1');
});

it('audits the user menu in its open state', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->wait(1)
        ->assertSee('Log out');

    assertNoAccessibilityIssuesExceptContrast($page);
});

it('navigates through the mobile menu at a mobile viewport', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->resize(390, 844)
        ->wait(1)
        ->assertNoSmoke();

    assertNoAccessibilityIssuesExceptContrast($page);

    $page->click('[aria-label="Menu"]')
        ->wait(1)
        ->assertSee('Categories');

    assertNoAccessibilityIssuesExceptContrast($page);

    $page->click('[role="menu"] a[href$="/tasks"]')
        ->assertPathIs('/tasks')
        ->assertSee('My tasks');
});

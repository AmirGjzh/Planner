<?php

use App\Models\Plan;
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

it('has no smoke or accessibility issues on the plans page', function () {
    Plan::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Sprint',
        'start_date' => now()->toDateString(),
        'finish_date' => now()->addDays(7)->toDateString(),
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Plans')
        ->assertPathIs('/plans')
        ->wait(1)
        ->assertSee('My plans')
        ->assertSee('Sprint');

    $page->assertNoSmoke();
    assertNoAccessibilityIssuesExceptContrast($page);
});

it('opens the mobile calendar dropdown with no accessibility issues', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Plans')
        ->assertPathIs('/plans')
        ->wait(1);

    // The calendar lives behind the mobile-only trigger (md:hidden), so gate it
    // between the sm and md breakpoints where the trigger renders.
    $page->resize(700, 900)
        ->click('Calendar')
        ->wait(1)
        ->assertSee('Reset');

    // The open panel hosts a calendar, not menu items — axe must pass expanded.
    assertNoAccessibilityIssuesExceptContrast($page);

    $page->assertNoSmoke();
});

it('opens the add plan modal with no accessibility issues', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Plans')
        ->assertPathIs('/plans')
        ->click('New plan')
        ->wait(1)
        ->assertSee('Plan name');

    // The open dialog must carry an accessible name; the close button must be labelled.
    assertNoAccessibilityIssuesExceptContrast($page);

    $page->assertNoSmoke();
});

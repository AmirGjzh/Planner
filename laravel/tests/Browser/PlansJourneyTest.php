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

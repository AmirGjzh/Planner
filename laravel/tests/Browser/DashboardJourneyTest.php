<?php

use App\Enums\TaskPriority;
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

function dashboardJourneyTask(User $user): void
{
    $category = $user->categories()->firstOrCreate(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Write docs', 'task_date' => now()->toDateString(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
}

it('has no smoke or accessibility issues on the dashboard', function () {
    dashboardJourneyTask($this->user);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard')
        ->wait(1)
        ->assertSee('Tasks needing attention')
        ->assertSee("This week's workload")
        ->assertSee('Write docs');

    $page->assertNoSmoke();
    assertNoAccessibilityIssuesExceptContrast($page);
});

it('deep-links an attention card into the filtered tasks page', function () {
    dashboardJourneyTask($this->user);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard')
        ->wait(1)
        ->click('a[href*="search="]')
        ->assertPathIs('/tasks')
        ->wait(1)
        ->assertSee('Write docs');

    $page->assertNoSmoke();
});

it('has no smoke or accessibility issues on the empty dashboard', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard')
        ->wait(1)
        ->assertSee('No tasks need your attention right now.');

    $page->assertNoSmoke();
    assertNoAccessibilityIssuesExceptContrast($page);
});

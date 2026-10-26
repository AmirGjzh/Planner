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

function reportsJourneyTask(User $user): void
{
    $category = $user->categories()->firstOrCreate(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Reported task', 'task_date' => now()->toDateString(), 'estimated_minutes' => 45,
        'day_before_alarm' => 0, 'category_id' => $category->id, 'done' => true,
    ]);
}

it('has no smoke or accessibility issues on the reports page', function () {
    reportsJourneyTask($this->user);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard')
        ->wait(1)
        ->click('amir')
        ->click('Reports')
        ->assertPathIs('/reports')
        ->wait(1)
        ->assertSee('My reports')
        ->assertSee('Tasks Summary')
        ->assertSee('Plans Summary')
        ->assertSee('Workload Chart')
        ->assertSee('Completed')
        ->assertSee('Remaining');

    $page->assertNoSmoke();
    assertNoAccessibilityIssuesExceptContrast($page);
});

it('opens the custom range calendar and returns to a preset', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard')
        ->wait(1)
        ->click('amir')
        ->click('Reports')
        ->assertPathIs('/reports')
        ->wait(1)
        ->assertDontSee('Date range')
        ->click('Custom')
        ->wait(1)
        ->assertSee('Date range')
        ->click('Date range')
        ->wait(1)
        ->assertSee('Reset')
        ->click('This week')
        ->wait(1)
        ->assertDontSee('Date range');

    $page->assertNoSmoke();
});

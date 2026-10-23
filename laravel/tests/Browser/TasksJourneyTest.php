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
    RateLimiter::clear('create-task:'.$this->user->id.'|127.0.0.1');
    RateLimiter::clear('toggle-task:'.$this->user->id.'|127.0.0.1');
});

it('has no smoke or accessibility issues on the tasks page', function () {
    $category = $this->user->categories()->create(['name' => 'Work']);
    $this->user->tasks()->create([
        'title' => 'Write docs', 'task_date' => now()->toDateString(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Tasks')
        ->assertPathIs('/tasks')
        ->wait(1)
        ->assertSee('My tasks')
        ->assertSee('Write docs');

    $page->assertNoSmoke();
    assertNoAccessibilityIssuesExceptContrast($page);
});

it('creates a task through the add modal', function () {
    $this->user->categories()->create(['name' => 'Work']);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Tasks')
        ->assertPathIs('/tasks')
        ->click('New task')
        ->wait(1)
        ->assertSee('Task title');

    // The open dialog must carry an accessible name; the close button must be labelled.
    assertNoAccessibilityIssuesExceptContrast($page);

    $page->fill('add_title', 'Water plants')
        ->fill('add_estimated_minutes', '30')
        ->fill('add_alarm_days', '0')
        ->click('Select priority')
        ->click('[data-value="medium"]:visible')
        ->click('Select category')
        // The option's padded slot text defeats the harness's exact getByText match; the
        // :visible pseudo scopes to the open popup (SelectDigitSearchTest precedent).
        ->click('[role="option"]:visible')
        ->click('Create')
        ->wait(1)
        ->assertSee('Water plants')
        ->assertSee('Your task created');

    $this->assertDatabaseHas('tasks', [
        'user_id' => $this->user->id,
        'title' => 'Water plants',
    ]);
    $page->assertNoSmoke();
});

it('completes and reopens a task through the card actions', function () {
    $category = $this->user->categories()->create(['name' => 'Work']);
    $this->user->tasks()->create([
        'title' => 'Write docs', 'task_date' => now()->toDateString(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Tasks')
        ->assertPathIs('/tasks')
        ->wait(1)
        ->click('Mark as completed')
        ->wait(1)
        // The card's hidden mobile label (<p class="sm:hidden">Complete</p>) shadows the
        // modal's exact-text button; target the submit by its Livewire contract instead.
        ->click('button[wire\:target="completeTask"]')
        ->wait(1)
        ->assertSee('Your task completed')
        // The card's status badge was removed (UC-08) — the completed state is now pinned by the
        // reopen action button, which only renders on a done card.
        ->assertSee('Re active');

    $this->assertDatabaseHas('tasks', [
        'user_id' => $this->user->id,
        'title' => 'Write docs',
        'done' => true,
    ]);

    $page->click('Re active')
        ->wait(1)
        ->click('button[wire\:target="reopenTask"]')
        ->wait(1)
        ->assertSee('Your task re activated');

    $page->assertNoSmoke();
});

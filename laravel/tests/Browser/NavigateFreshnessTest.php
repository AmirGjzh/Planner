<?php

use App\Enums\TaskPriority;
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

it('renders fresh plan progress after browser back navigation', function () {
    $plan = Plan::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Sprint',
        'start_date' => now()->toDateString(),
        'finish_date' => now()->addDays(7)->toDateString(),
    ]);

    $category = $this->user->categories()->create(['name' => 'Work']);
    $this->user->tasks()->create([
        'title' => 'Write docs',
        'task_date' => now()->toDateString(),
        'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium,
        'day_before_alarm' => 0,
        'category_id' => $category->id,
        'plan_id' => $plan->id,
        'done' => false,
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
        ->assertSee('0%');

    // Mutate the plan's data from a different page. Navigate's back/forward
    // snapshot cache would restore the pre-mutation DOM above; the listener
    // in app.js cancels that swap and reloads so the traversal renders fresh.
    $page->click('amir')
        ->click('Tasks')
        ->assertPathIs('/tasks')
        ->wait(1)
        ->click('Mark as completed')
        ->wait(1)
        // The card's hidden mobile label (<p class="sm:hidden">Complete</p>)
        // shadows the modal's exact-text button; target the submit button by
        // its Livewire contract instead.
        ->click('button[wire\:target="completeTask"]')
        ->wait(1);

    $page->script('history.back()');
    $page->wait(1.5)
        ->assertPathIs('/plans')
        ->assertSee('100%');
});

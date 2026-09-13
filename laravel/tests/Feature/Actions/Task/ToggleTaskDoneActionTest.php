<?php

use App\Actions\Task\ToggleTaskDoneAction;
use App\Enums\TaskPriority;
use App\Enums\ToggleTaskDoneResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

function toggleTaskRequest(): Request
{
    return Request::create('/tasks', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

function toggleTaskRateLimitKey(User $user): string
{
    return 'toggle-task:'.$user->id.'|127.0.0.1';
}

it('toggles a task from not done to done', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(toggleTaskRateLimitKey($user));

    app(ToggleTaskDoneAction::class)->execute($user, $task, toggleTaskRequest());

    expect($task->fresh()->done)->toBeTrue();
});

it('toggles a task from done to not done', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id, 'done' => true,
    ]);
    RateLimiter::clear(toggleTaskRateLimitKey($user));

    expect($task->done)->toBeTrue();

    app(ToggleTaskDoneAction::class)->execute($user, $task, toggleTaskRequest());

    expect($task->fresh()->done)->toBeFalse();
});

it('prevents toggling a task that belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $category = $user1->categories()->create(['name' => 'Work']);
    $task = $user1->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $this->expectException(HttpException::class);

    app(ToggleTaskDoneAction::class)->execute($user2, $task, toggleTaskRequest());
});

it('logs toggle events', function () {
    Log::spy();

    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(toggleTaskRateLimitKey($user));

    app(ToggleTaskDoneAction::class)->execute($user, $task, toggleTaskRequest());

    Log::shouldHaveReceived('info')
        ->with('Task toggled.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
                && $context['new_status'] === true
        ));
});

it('rate limits excessive toggles', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(toggleTaskRateLimitKey($user));

    foreach (range(1, 20) as $attempt) {
        app(ToggleTaskDoneAction::class)->execute($user, $task, toggleTaskRequest());
    }

    $result = app(ToggleTaskDoneAction::class)->execute($user, $task, toggleTaskRequest());

    expect($result)->toBe(ToggleTaskDoneResult::RateLimited);
});

it('allows toggling again after rate limit resets', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(toggleTaskRateLimitKey($user));

    foreach (range(1, 20) as $attempt) {
        app(ToggleTaskDoneAction::class)->execute($user, $task, toggleTaskRequest());
    }

    $this->travel(61)->seconds();

    $result = app(ToggleTaskDoneAction::class)->execute($user, $task, toggleTaskRequest());

    expect($result)->toBe(ToggleTaskDoneResult::Toggled);
});

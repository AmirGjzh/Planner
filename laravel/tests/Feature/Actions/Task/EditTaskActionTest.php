<?php

use App\Actions\Task\EditTaskAction;
use App\Enums\EditTaskResult;
use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

function editTaskRequest(): Request
{
    return Request::create('/tasks', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

function editTaskRateLimitKey(User $user): string
{
    return 'edit-task:'.$user->id.'|127.0.0.1';
}

it('updates a task successfully', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(editTaskRateLimitKey($user));

    $result = app(EditTaskAction::class)->execute(
        $user, $task, 'Updated', 'New desc', '2026-06-15', 60, TaskPriority::High, 2, $category->id, null, editTaskRequest(),
    );

    expect($result)->toBe(EditTaskResult::Updated);
    expect($task->fresh()->title)->toBe('Updated');
    expect($task->fresh()->description)->toBe('New desc');
    expect($task->fresh()->task_date->format('Y-m-d'))->toBe('2026-06-15');
    expect($task->fresh()->estimated_minutes)->toBe(60);
    expect($task->fresh()->priority)->toBe(TaskPriority::High);
    expect($task->fresh()->day_before_alarm)->toBe(2);
});

it('updates a task with a plan assigned', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $plan = $user->plans()->create(['name' => 'Sprint', 'start_date' => '2026-06-01', 'finish_date' => '2026-06-30']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(editTaskRateLimitKey($user));

    $result = app(EditTaskAction::class)->execute(
        $user, $task, 'Original', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, $plan->id, editTaskRequest(),
    );

    expect($result)->toBe(EditTaskResult::Updated);
    expect($task->fresh()->plan_id)->toBe($plan->id);
});

it('prevents editing a task that belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $category = $user1->categories()->create(['name' => 'Work']);
    $task = $user1->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $this->expectException(HttpException::class);

    app(EditTaskAction::class)->execute(
        $user2, $task, 'Hacked', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, editTaskRequest(),
    );
});

it('returns rate limited after repeated failures', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(editTaskRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(EditTaskAction::class)->execute(
            $user, $task, 'Attempt '.$attempt, null, '2026-06-01', 30, TaskPriority::Medium, 0, 999, null, editTaskRequest(),
        );
    }

    $result = app(EditTaskAction::class)->execute(
        $user, $task, 'Blocked', null, '2026-06-01', 30, TaskPriority::Medium, 0, 999, null, editTaskRequest(),
    );

    expect($result)->toBe(EditTaskResult::RateLimited);
});

it('allows editing again after one minute', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(editTaskRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(EditTaskAction::class)->execute(
            $user, $task, 'Attempt '.$attempt, null, '2026-06-01', 30, TaskPriority::Medium, 0, 999, null, editTaskRequest(),
        );
    }

    $this->travel(61)->seconds();

    $result = app(EditTaskAction::class)->execute(
        $user, $task, 'After cooldown', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, editTaskRequest(),
    );

    expect($result)->toBe(EditTaskResult::Updated);
    expect($task->fresh()->title)->toBe('After cooldown');
});

it('allows editing again after a successful edit clears the limiter', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(editTaskRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(EditTaskAction::class)->execute(
            $user, $task, 'Attempt '.$attempt, null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, editTaskRequest(),
        );
    }

    $result = app(EditTaskAction::class)->execute(
        $user, $task, 'After clears', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, editTaskRequest(),
    );

    expect($result)->toBe(EditTaskResult::Updated);
});

it('returns invalid category on edit', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(editTaskRateLimitKey($user));

    $result = app(EditTaskAction::class)->execute(
        $user, $task, 'Test', null, '2026-06-01', 30, TaskPriority::Medium, 0, 999, null, editTaskRequest(),
    );

    expect($result)->toBe(EditTaskResult::InvalidCategory);
});

it('returns invalid plan on edit', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(editTaskRateLimitKey($user));

    $result = app(EditTaskAction::class)->execute(
        $user, $task, 'Test', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, 999, editTaskRequest(),
    );

    expect($result)->toBe(EditTaskResult::InvalidPlan);
});

it('logs edit events', function () {
    Log::spy();

    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear(editTaskRateLimitKey($user));

    app(EditTaskAction::class)->execute(
        $user, $task, 'Test', null, '2026-06-01', 30, TaskPriority::Medium, 0, 999, null, editTaskRequest(),
    );

    Log::shouldHaveReceived('warning')
        ->with('Task edit failed, invalid category.', Mockery::on(
            fn (array $context) => $context['task_id'] === $task->id
        ));

    foreach (range(1, 5) as $attempt) {
        app(EditTaskAction::class)->execute(
            $user, $task, 'Attempt '.$attempt, null, '2026-06-01', 30, TaskPriority::Medium, 0, 999, null, editTaskRequest(),
        );
    }

    Log::shouldHaveReceived('warning')
        ->with('Task edit rate limited.', Mockery::on(
            fn (array $context) => isset($context['available_in'])
        ));

    RateLimiter::clear(editTaskRateLimitKey($user));

    app(EditTaskAction::class)->execute(
        $user, $task, 'Renamed', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, editTaskRequest(),
    );

    Log::shouldHaveReceived('info')
        ->with('Task updated.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));
});

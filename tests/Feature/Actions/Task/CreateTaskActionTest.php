<?php

use App\Actions\Task\CreateTaskAction;
use App\Enums\CreateTaskResult;
use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

function createTaskRequest(): Request
{
    return Request::create('/task-page', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

function createTaskRateLimitKey(User $user): string
{
    return 'create-task:'.$user->id.'|127.0.0.1';
}

it('creates a task successfully', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createTaskRateLimitKey($user));

    $result = app(CreateTaskAction::class)->execute(
        $user, 'Test task', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, createTaskRequest(),
    );

    expect($result)->toBe(CreateTaskResult::Created);
    expect($user->tasks()->where('title', 'Test task')->exists())->toBeTrue();
});

it('creates a task with all optional fields', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $plan = $user->plans()->create(['name' => 'Sprint', 'start_date' => '2026-06-01', 'finish_date' => '2026-06-30']);
    RateLimiter::clear(createTaskRateLimitKey($user));

    $result = app(CreateTaskAction::class)->execute(
        $user, 'Full task', 'A description', '2026-06-15', 60, TaskPriority::High, 2, $category->id, $plan->id, createTaskRequest(),
    );

    expect($result)->toBe(CreateTaskResult::Created);
    expect($user->tasks()->where('title', 'Full task')->exists())->toBeTrue();
});

it('returns rate limited after repeated attempts', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createTaskRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(CreateTaskAction::class)->execute(
            $user, 'Attempt '.$attempt, null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, createTaskRequest(),
        );
    }

    $result = app(CreateTaskAction::class)->execute(
        $user, 'Blocked', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, createTaskRequest(),
    );

    expect($result)->toBe(CreateTaskResult::RateLimited);
});

it('allows creation again after one minute', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createTaskRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(CreateTaskAction::class)->execute(
            $user, 'Attempt '.$attempt, null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, createTaskRequest(),
        );
    }

    $this->travel(61)->seconds();

    $result = app(CreateTaskAction::class)->execute(
        $user, 'After cooldown', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, createTaskRequest(),
    );

    expect($result)->toBe(CreateTaskResult::Created);
    expect($user->tasks()->where('title', 'After cooldown')->exists())->toBeTrue();
});

it('returns invalid category when category does not exist', function () {
    $user = User::factory()->create();
    RateLimiter::clear(createTaskRateLimitKey($user));

    $result = app(CreateTaskAction::class)->execute(
        $user, 'Test', null, '2026-06-01', 30, TaskPriority::Medium, 0, 999, null, createTaskRequest(),
    );

    expect($result)->toBe(CreateTaskResult::InvalidCategory);
});

it('returns invalid category when category belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $category = $user1->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createTaskRateLimitKey($user2));

    $result = app(CreateTaskAction::class)->execute(
        $user2, 'Test', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, createTaskRequest(),
    );

    expect($result)->toBe(CreateTaskResult::InvalidCategory);
});

it('returns invalid plan when plan does not exist', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createTaskRateLimitKey($user));

    $result = app(CreateTaskAction::class)->execute(
        $user, 'Test', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, 999, createTaskRequest(),
    );

    expect($result)->toBe(CreateTaskResult::InvalidPlan);
});

it('returns invalid plan when plan belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $category = $user2->categories()->create(['name' => 'Work']);
    $plan = $user1->plans()->create(['name' => 'Sprint', 'start_date' => '2026-06-01', 'finish_date' => '2026-06-30']);
    RateLimiter::clear(createTaskRateLimitKey($user2));

    $result = app(CreateTaskAction::class)->execute(
        $user2, 'Test', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, $plan->id, createTaskRequest(),
    );

    expect($result)->toBe(CreateTaskResult::InvalidPlan);
});

it('logs creation events', function () {
    Log::spy();

    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createTaskRateLimitKey($user));

    app(CreateTaskAction::class)->execute(
        $user, 'Test', null, '2026-06-01', 30, TaskPriority::Medium, 0, 999, null, createTaskRequest(),
    );

    Log::shouldHaveReceived('warning')
        ->with('Task creation failed, invalid category.', Mockery::on(
            fn (array $context) => $context['category_id'] === 999
        ));

    foreach (range(1, 5) as $attempt) {
        app(CreateTaskAction::class)->execute(
            $user, 'Attempt '.$attempt, null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, createTaskRequest(),
        );
    }

    Log::shouldHaveReceived('warning')
        ->with('Task creation rate limited.', Mockery::on(
            fn (array $context) => isset($context['seconds_remaining'])
        ));

    RateLimiter::clear(createTaskRateLimitKey($user));

    app(CreateTaskAction::class)->execute(
        $user, 'Final', null, '2026-06-01', 30, TaskPriority::Medium, 0, $category->id, null, createTaskRequest(),
    );

    Log::shouldHaveReceived('info')
        ->with('Task created.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id && $context['title'] === 'Final'
        ));
});

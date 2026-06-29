<?php

use App\Actions\Task\DeleteTaskAction;
use App\Enums\DeleteTaskResult;
use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

it('deletes a task successfully', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $result = app(DeleteTaskAction::class)->execute($user, $task->id);

    expect($result)->toBe(DeleteTaskResult::Deleted);
    expect($user->tasks()->where('title', 'Test task')->exists())->toBeFalse();
});

it('prevents deleting a task that belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $category = $user1->categories()->create(['name' => 'Work']);
    $task = $user1->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $this->expectException(ModelNotFoundException::class);

    app(DeleteTaskAction::class)->execute($user2, $task->id);
});

it('logs deletion events', function () {
    Log::spy();

    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    app(DeleteTaskAction::class)->execute($user, $task->id);

    Log::shouldHaveReceived('info')
        ->with('Task deleted.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id && $context['title'] === 'Test task'
        ));
});

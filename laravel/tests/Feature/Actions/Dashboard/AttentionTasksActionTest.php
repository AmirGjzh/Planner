<?php

use App\Actions\Dashboard\AttentionTasksAction;
use App\Models\User;

function attentionTask(User $user, string $title, array $overrides = []): void
{
    $user->tasks()->create(array_merge([
        'title' => $title,
        'task_date' => now()->format('Y-m-d'),
        'estimated_minutes' => 30,
        'day_before_alarm' => 0,
        'category_id' => $user->categories()->firstOrCreate(['name' => 'Work'])->id,
    ], $overrides));
}

it('returns tasks within their notification window', function () {
    $user = User::factory()->create();
    attentionTask($user, 'Approaching', ['task_date' => now()->addDays(2)->format('Y-m-d'), 'day_before_alarm' => 3]);

    $tasks = app(AttentionTasksAction::class)->execute($user);

    expect($tasks->pluck('title')->all())->toBe(['Approaching']);
});

it('includes overdue tasks', function () {
    $user = User::factory()->create();
    attentionTask($user, 'Overdue', ['task_date' => now()->subDays(2)->format('Y-m-d')]);

    $tasks = app(AttentionTasksAction::class)->execute($user);

    expect($tasks->pluck('title')->all())->toBe(['Overdue']);
});

it('excludes tasks whose alarm has not started yet', function () {
    $user = User::factory()->create();
    attentionTask($user, 'Far future', ['task_date' => now()->addDays(5)->format('Y-m-d'), 'day_before_alarm' => 1]);

    $tasks = app(AttentionTasksAction::class)->execute($user);

    expect($tasks)->toBeEmpty();
});

it('hides future tasks when the alarm is set for the day itself', function () {
    $user = User::factory()->create();
    attentionTask($user, 'Tomorrow', ['task_date' => now()->addDay()->format('Y-m-d'), 'day_before_alarm' => 0]);

    $tasks = app(AttentionTasksAction::class)->execute($user);

    expect($tasks)->toBeEmpty();
});

it('excludes completed tasks', function () {
    $user = User::factory()->create();
    attentionTask($user, 'Done', ['done' => true]);
    attentionTask($user, 'Pending');

    $tasks = app(AttentionTasksAction::class)->execute($user);

    expect($tasks->pluck('title')->all())->toBe(['Pending']);
});

it('excludes tasks belonging to another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    attentionTask($other, 'Private');

    $tasks = app(AttentionTasksAction::class)->execute($user);

    expect($tasks)->toBeEmpty();
});

it('orders the attention list by task date ascending', function () {
    $user = User::factory()->create();
    attentionTask($user, 'Later', ['task_date' => now()->addDay()->format('Y-m-d'), 'day_before_alarm' => 1]);
    attentionTask($user, 'Soon', ['task_date' => now()->format('Y-m-d')]);

    $tasks = app(AttentionTasksAction::class)->execute($user);

    expect($tasks->pluck('title')->all())->toBe(['Soon', 'Later']);
});

<?php

use App\Actions\Plan\CompletePlanAction;
use App\Enums\CompletePlanResult;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('completes a plan when all tasks are done', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $category = $user->categories()->create(['name' => 'General']);
    $user->tasks()->create(['title' => 'A', 'task_date' => '2026-01-15', 'estimated_minutes' => 30, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);
    $user->tasks()->create(['title' => 'B', 'task_date' => '2026-01-16', 'estimated_minutes' => 45, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);

    $result = app(CompletePlanAction::class)->execute($user, $plan);

    expect($result)->toBe(CompletePlanResult::Completed);
    expect($plan->fresh()->done)->toBeTrue();
});

it('completes a plan with no tasks', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    $result = app(CompletePlanAction::class)->execute($user, $plan);

    expect($result)->toBe(CompletePlanResult::Completed);
    expect($plan->fresh()->done)->toBeTrue();
});

it('blocks completing a plan with undone tasks', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $category = $user->categories()->create(['name' => 'General']);
    $user->tasks()->create(['title' => 'A', 'task_date' => '2026-01-15', 'estimated_minutes' => 30, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);
    $user->tasks()->create(['title' => 'B', 'task_date' => '2026-01-16', 'estimated_minutes' => 45, 'plan_id' => $plan->id, 'category_id' => $category->id]);

    $result = app(CompletePlanAction::class)->execute($user, $plan);

    expect($result)->toBe(CompletePlanResult::HasUndoneTasks);
    expect($plan->fresh()->done)->toBeFalse();
});

it('prevents completing a plan that belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $plan = $user1->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    $this->expectException(HttpException::class);

    app(CompletePlanAction::class)->execute($user2, $plan);
});

it('logs completion events', function () {
    Log::spy();

    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'General']);

    $blocked = $user->plans()->create(['name' => 'Blocked', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $user->tasks()->create(['title' => 'B', 'task_date' => '2026-01-16', 'estimated_minutes' => 45, 'plan_id' => $blocked->id, 'category_id' => $category->id]);

    app(CompletePlanAction::class)->execute($user, $blocked);

    Log::shouldHaveReceived('warning')
        ->with('Plan completion blocked, has undone tasks.', Mockery::on(
            fn (array $context) => $context['plan_id'] === $blocked->id
        ));

    $plan = $user->plans()->create(['name' => 'Done', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);
    $user->tasks()->create(['title' => 'A', 'task_date' => '2026-02-15', 'estimated_minutes' => 30, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);

    app(CompletePlanAction::class)->execute($user, $plan);

    Log::shouldHaveReceived('info')
        ->with('Plan completed.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id && $context['plan_id'] === $plan->id
        ));
});

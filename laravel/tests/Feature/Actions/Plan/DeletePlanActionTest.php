<?php

use App\Actions\Plan\DeletePlanAction;
use App\Enums\DeletePlanResult;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('deletes a plan successfully', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    $result = app(DeletePlanAction::class)->execute($user, $plan);

    expect($result)->toBe(DeletePlanResult::Deleted);
    expect($user->plans()->where('name', 'Work')->exists())->toBeFalse();
});

it('prevents deleting a plan that belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $plan = $user1->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    $this->expectException(HttpException::class);

    app(DeletePlanAction::class)->execute($user2, $plan);
});

it('prevents deleting a plan that has tasks', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $user->tasks()->create([
        'title' => 'Test task',
        'task_date' => '2026-01-15',
        'estimated_minutes' => 30,
        'plan_id' => $plan->id,
        'category_id' => $user->categories()->create(['name' => 'General'])->id,
    ]);

    $result = app(DeletePlanAction::class)->execute($user, $plan);

    expect($result)->toBe(DeletePlanResult::HasTasks);
    expect($user->plans()->where('name', 'Work')->exists())->toBeTrue();
});

it('logs deletion events', function () {
    Log::spy();

    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    $planWithTasks = $user->plans()->create(['name' => 'Personal', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);
    $user->tasks()->create([
        'title' => 'Test',
        'task_date' => '2026-02-15',
        'estimated_minutes' => 30,
        'plan_id' => $planWithTasks->id,
        'category_id' => $user->categories()->create(['name' => 'General'])->id,
    ]);

    app(DeletePlanAction::class)->execute($user, $planWithTasks);

    Log::shouldHaveReceived('warning')
        ->with('Plan deletion failed, has tasks assigned.', Mockery::on(
            fn (array $context) => $context['name'] === 'Personal'
        ));

    app(DeletePlanAction::class)->execute($user, $plan);

    Log::shouldHaveReceived('info')
        ->with('Plan deleted.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id && $context['name'] === 'Work'
        ));
});

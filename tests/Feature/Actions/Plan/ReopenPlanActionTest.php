<?php

use App\Actions\Plan\ReopenPlanAction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('reopens a completed plan', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    app(ReopenPlanAction::class)->execute($user, $plan);

    expect($plan->fresh()->done)->toBeFalse();
});

it('reopens a plan even when it still has tasks', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);
    $user->tasks()->create([
        'title' => 'Test task',
        'task_date' => '2026-01-15',
        'estimated_minutes' => 30,
        'plan_id' => $plan->id,
        'category_id' => $user->categories()->create(['name' => 'General'])->id,
    ]);

    app(ReopenPlanAction::class)->execute($user, $plan);

    expect($plan->fresh()->done)->toBeFalse();
});

it('prevents reopening a plan that belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $plan = $user1->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    $this->expectException(HttpException::class);

    app(ReopenPlanAction::class)->execute($user2, $plan);
});

it('logs reopen events', function () {
    Log::spy();

    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    app(ReopenPlanAction::class)->execute($user, $plan);

    Log::shouldHaveReceived('info')
        ->with('Plan reopened.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id && $context['plan_id'] === $plan->id
        ));
});

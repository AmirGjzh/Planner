<?php

use App\Actions\Plan\EditPlanAction;
use App\Enums\EditPlanResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

function editPlanRequest(): Request
{
    return Request::create('/plans', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

function editPlanRateLimitKey(User $user): string
{
    return 'edit-plan:'.$user->id.'|127.0.0.1';
}

it('updates a plan successfully', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    RateLimiter::clear(editPlanRateLimitKey($user));

    $result = app(EditPlanAction::class)->execute($user, $plan, 'Personal', 'Updated desc', ['start' => '2026-02-01', 'end' => '2026-02-28'], editPlanRequest());

    expect($result)->toBe(EditPlanResult::Updated);
    expect($plan->fresh()->name)->toBe('Personal');
    expect($plan->fresh()->description)->toBe('Updated desc');
    expect($plan->fresh()->start_date->format('Y-m-d'))->toBe('2026-02-01');
    expect($plan->fresh()->finish_date->format('Y-m-d'))->toBe('2026-02-28');
});

it('detects duplicate plan name on edit', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $plan = $user->plans()->create(['name' => 'Personal', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);
    RateLimiter::clear(editPlanRateLimitKey($user));

    $result = app(EditPlanAction::class)->execute($user, $plan, 'Work', null, ['start' => '2026-03-01', 'end' => '2026-03-31'], editPlanRequest());

    expect($result)->toBe(EditPlanResult::AlreadyExists);
});

it('allows keeping the same plan name', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    RateLimiter::clear(editPlanRateLimitKey($user));

    $result = app(EditPlanAction::class)->execute($user, $plan, 'Work', null, ['start' => '2026-01-01', 'end' => '2026-01-31'], editPlanRequest());

    expect($result)->toBe(EditPlanResult::Updated);
});

it('prevents editing a plan that belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $plan = $user1->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    $this->expectException(HttpException::class);

    app(EditPlanAction::class)->execute($user2, $plan, 'Personal', null, ['start' => '2026-02-01', 'end' => '2026-02-28'], editPlanRequest());
});

it('returns rate limited after repeated edit attempts', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $user->plans()->create(['name' => 'Existing', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);
    RateLimiter::clear(editPlanRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(EditPlanAction::class)->execute($user, $plan, 'Existing', null, ['start' => '2026-02-01', 'end' => '2026-02-28'], editPlanRequest());
    }

    $result = app(EditPlanAction::class)->execute($user, $plan, 'Blocked', null, ['start' => '2026-03-01', 'end' => '2026-03-31'], editPlanRequest());

    expect($result)->toBe(EditPlanResult::RateLimited);
});

it('allows editing again after one minute', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $user->plans()->create(['name' => 'Existing', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);
    RateLimiter::clear(editPlanRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(EditPlanAction::class)->execute($user, $plan, 'Existing', null, ['start' => '2026-02-01', 'end' => '2026-02-28'], editPlanRequest());
    }

    $this->travel(61)->seconds();

    $result = app(EditPlanAction::class)->execute($user, $plan, 'Personal', null, ['start' => '2026-03-01', 'end' => '2026-03-31'], editPlanRequest());

    expect($result)->toBe(EditPlanResult::Updated);
    expect($plan->fresh()->name)->toBe('Personal');
});

it('logs edit events', function () {
    Log::spy();

    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $user->plans()->create(['name' => 'Personal', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);
    RateLimiter::clear(editPlanRateLimitKey($user));

    app(EditPlanAction::class)->execute($user, $plan, 'Personal', null, ['start' => '2026-03-01', 'end' => '2026-03-31'], editPlanRequest());

    Log::shouldHaveReceived('warning')
        ->with('Plan edit failed, already exists.', Mockery::on(
            fn (array $context) => $context['plan_id'] === $plan->id
        ));

    foreach (range(1, 5) as $attempt) {
        app(EditPlanAction::class)->execute($user, $plan, 'Personal', null, ['start' => '2026-04-01', 'end' => '2026-04-30'], editPlanRequest());
    }

    Log::shouldHaveReceived('warning')
        ->with('Plan edit rate limited.', Mockery::on(
            fn (array $context) => isset($context['available_in'])
        ));

    RateLimiter::clear(editPlanRateLimitKey($user));

    app(EditPlanAction::class)->execute($user, $plan, 'Renamed', null, ['start' => '2026-05-01', 'end' => '2026-05-31'], editPlanRequest());

    Log::shouldHaveReceived('info')
        ->with('Plan updated.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));
});

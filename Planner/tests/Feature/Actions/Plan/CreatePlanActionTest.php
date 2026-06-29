<?php

use App\Actions\Plan\CreatePlanAction;
use App\Enums\CreatePlanResult;
use App\Models\User;
use App\View\Components\DateRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

function createPlanRequest(): Request
{
    return Request::create('/plan-page', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

function createPlanRateLimitKey(User $user): string
{
    return 'create-plan:'.$user->id.'|127.0.0.1';
}

it('creates a plan successfully', function () {
    $user = User::factory()->create();
    RateLimiter::clear(createPlanRateLimitKey($user));

    $result = app(CreatePlanAction::class)->execute($user, 'Work', null, new DateRange('2026-01-01', '2026-01-31'), createPlanRequest());

    expect($result)->toBe(CreatePlanResult::Created);
    expect($user->plans()->where('name', 'Work')->exists())->toBeTrue();
});

it('detects duplicate plan name for the same user', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    RateLimiter::clear(createPlanRateLimitKey($user));

    $result = app(CreatePlanAction::class)->execute($user, 'Work', null, new DateRange('2026-02-01', '2026-02-28'), createPlanRequest());

    expect($result)->toBe(CreatePlanResult::AlreadyExists);
});

it('allows different users to have the same plan name', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user1->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    RateLimiter::clear(createPlanRateLimitKey($user2));

    $result = app(CreatePlanAction::class)->execute($user2, 'Work', null, new DateRange('2026-02-01', '2026-02-28'), createPlanRequest());

    expect($result)->toBe(CreatePlanResult::Created);
    expect($user2->plans()->where('name', 'Work')->exists())->toBeTrue();
});

it('returns rate limited after repeated attempts', function () {
    $user = User::factory()->create();
    RateLimiter::clear(createPlanRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(CreatePlanAction::class)->execute($user, 'Attempt '.$attempt, null, new DateRange('2026-01-01', '2026-01-31'), createPlanRequest());
    }

    $result = app(CreatePlanAction::class)->execute($user, 'Blocked', null, new DateRange('2026-02-01', '2026-02-28'), createPlanRequest());

    expect($result)->toBe(CreatePlanResult::RateLimited);
});

it('allows creation again after one minute', function () {
    $user = User::factory()->create();
    RateLimiter::clear(createPlanRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(CreatePlanAction::class)->execute($user, 'Attempt '.$attempt, null, new DateRange('2026-01-01', '2026-01-31'), createPlanRequest());
    }

    $this->travel(61)->seconds();

    $result = app(CreatePlanAction::class)->execute($user, 'Work', null, new DateRange('2026-03-01', '2026-03-31'), createPlanRequest());

    expect($result)->toBe(CreatePlanResult::Created);
    expect($user->plans()->where('name', 'Work')->exists())->toBeTrue();
});

it('logs creation events', function () {
    Log::spy();

    $user = User::factory()->create();
    RateLimiter::clear(createPlanRateLimitKey($user));

    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    app(CreatePlanAction::class)->execute($user, 'Work', null, new DateRange('2026-02-01', '2026-02-28'), createPlanRequest());

    Log::shouldHaveReceived('warning')
        ->with('Plan creation failed, already exists.', Mockery::on(
            fn (array $context) => $context['name'] === 'Work'
        ));

    foreach (range(1, 5) as $attempt) {
        app(CreatePlanAction::class)->execute($user, 'Attempt '.$attempt, null, new DateRange('2026-03-01', '2026-03-31'), createPlanRequest());
    }

    Log::shouldHaveReceived('warning')
        ->with('Plan creation rate limited.', Mockery::on(
            fn (array $context) => isset($context['seconds_remaining'])
        ));

    RateLimiter::clear(createPlanRateLimitKey($user));

    app(CreatePlanAction::class)->execute($user, 'Personal', null, new DateRange('2026-04-01', '2026-04-30'), createPlanRequest());

    Log::shouldHaveReceived('info')
        ->with('Plan created.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id && $context['name'] === 'Personal'
        ));
});

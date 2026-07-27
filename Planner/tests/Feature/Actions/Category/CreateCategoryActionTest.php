<?php

use App\Actions\Category\CreateCategoryAction;
use App\Enums\CreateCategoryResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

function createCategoryRequest(): Request
{
    return Request::create('/categories', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

function createCategoryRateLimitKey(User $user): string
{
    return 'create-category:'.$user->id.'|127.0.0.1';
}

it('creates a category successfully', function () {
    $user = User::factory()->create();
    RateLimiter::clear(createCategoryRateLimitKey($user));

    $result = app(CreateCategoryAction::class)->execute($user, 'Work', createCategoryRequest());

    expect($result)->toBe(CreateCategoryResult::Created);
    expect($user->categories()->where('name', 'Work')->exists())->toBeTrue();
});

it('detects duplicate category name for the same user', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createCategoryRateLimitKey($user));

    $result = app(CreateCategoryAction::class)->execute($user, 'Work', createCategoryRequest());

    expect($result)->toBe(CreateCategoryResult::AlreadyExists);
});

it('allows different users to have the same category name', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user1->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createCategoryRateLimitKey($user2));

    $result = app(CreateCategoryAction::class)->execute($user2, 'Work', createCategoryRequest());

    expect($result)->toBe(CreateCategoryResult::Created);
    expect($user2->categories()->where('name', 'Work')->exists())->toBeTrue();
});

it('returns rate limited after repeated attempts', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createCategoryRateLimitKey($user));

    foreach (range(1, 5) as $ignored) {
        app(CreateCategoryAction::class)->execute($user, 'Work', createCategoryRequest());
    }

    $result = app(CreateCategoryAction::class)->execute($user, 'Work', createCategoryRequest());

    expect($result)->toBe(CreateCategoryResult::RateLimited);
});

it('allows creation again after one minute', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(createCategoryRateLimitKey($user));

    foreach (range(1, 5) as $ignored) {
        app(CreateCategoryAction::class)->execute($user, 'Work', createCategoryRequest());
    }

    $this->travel(61)->seconds();

    $result = app(CreateCategoryAction::class)->execute($user, 'Personal', createCategoryRequest());

    expect($result)->toBe(CreateCategoryResult::Created);
    expect($user->categories()->where('name', 'Personal')->exists())->toBeTrue();
});

it('logs duplicate attempt warning', function () {
    Log::spy();

    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    app(CreateCategoryAction::class)->execute($user, 'Work', createCategoryRequest());

    Log::shouldHaveReceived('warning')
        ->with('Category creation failed, already exists.', Mockery::on(
            fn (array $context) => $context['name'] === 'Work'
        ));
});

it('logs successful creation info', function () {
    Log::spy();

    $user = User::factory()->create();

    app(CreateCategoryAction::class)->execute($user, 'Work', createCategoryRequest());

    Log::shouldHaveReceived('info')
        ->with('Category created.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id && $context['name'] === 'Work'
        ));
});

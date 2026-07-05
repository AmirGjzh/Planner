<?php

use App\Actions\Category\EditCategoryAction;
use App\Enums\EditCategoryResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

function editCategoryRequest(): Request
{
    return Request::create('/category-page', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

function editCategoryRateLimitKey(User $user): string
{
    return 'edit-category:'.$user->id.'|127.0.0.1';
}

it('updates a category name successfully', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    $result = app(EditCategoryAction::class)->execute($user, $category, 'Personal', editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::Updated);
    expect($category->fresh()->name)->toBe('Personal');
});

it('detects duplicate category name on edit', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $category = $user->categories()->create(['name' => 'Personal']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    $result = app(EditCategoryAction::class)->execute($user, $category, 'Work', editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::AlreadyExists);
});

it('allows keeping the same category name', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    $result = app(EditCategoryAction::class)->execute($user, $category, 'Work', editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::Updated);
});

it('prevents editing a category that belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $category = $user1->categories()->create(['name' => 'Work']);

    $this->expectException(HttpException::class);

    app(EditCategoryAction::class)->execute($user2, $category, 'Personal', editCategoryRequest());
});

it('returns rate limited after repeated edit attempts', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(EditCategoryAction::class)->execute($user, $category, 'Attempt '.$attempt, editCategoryRequest());
    }

    $result = app(EditCategoryAction::class)->execute($user, $category, 'Blocked', editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::RateLimited);
});

it('allows editing again after one minute', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(EditCategoryAction::class)->execute($user, $category, 'Attempt '.$attempt, editCategoryRequest());
    }

    $this->travel(61)->seconds();

    $result = app(EditCategoryAction::class)->execute($user, $category, 'Personal', editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::Updated);
    expect($category->fresh()->name)->toBe('Personal');
});

it('logs edit events', function () {
    Log::spy();

    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    $user->categories()->create(['name' => 'Personal']);

    app(EditCategoryAction::class)->execute($user, $category, 'Personal', editCategoryRequest());

    Log::shouldHaveReceived('warning')
        ->with('Category edit failed, already exists.', Mockery::on(
            fn (array $context) => $context['category_id'] === $category->id
        ));

    foreach (range(1, 5) as $attempt) {
        app(EditCategoryAction::class)->execute($user, $category, 'Attempt '.$attempt, editCategoryRequest());
    }

    Log::shouldHaveReceived('warning')
        ->with('Category edit rate limited.', Mockery::on(
            fn (array $context) => isset($context['seconds_remaining'])
        ));

    RateLimiter::clear(editCategoryRateLimitKey($user));

    app(EditCategoryAction::class)->execute($user, $category, 'Renamed', editCategoryRequest());

    Log::shouldHaveReceived('info')
        ->with('Category updated.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));
});

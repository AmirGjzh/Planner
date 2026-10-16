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
    return Request::create('/categories', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
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
    $user->categories()->create(['name' => 'Work']);
    $category = $user->categories()->create(['name' => 'Personal']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    foreach (range(1, 5) as $ignored) {
        app(EditCategoryAction::class)->execute($user, $category, 'Work', editCategoryRequest());
    }

    $result = app(EditCategoryAction::class)->execute($user, $category, 'Work', editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::RateLimited);
});

it('allows editing again after one minute', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $category = $user->categories()->create(['name' => 'Personal']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    foreach (range(1, 5) as $ignored) {
        app(EditCategoryAction::class)->execute($user, $category, 'Work', editCategoryRequest());
    }

    $this->travel(61)->seconds();

    $result = app(EditCategoryAction::class)->execute($user, $category, 'Renamed', editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::Updated);
    expect($category->fresh()->name)->toBe('Renamed');
});

it('logs duplicate edit warning', function () {
    Log::spy();

    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->categories()->create(['name' => 'Personal']);

    app(EditCategoryAction::class)->execute($user, $category, 'Personal', editCategoryRequest());

    Log::shouldHaveReceived('warning')
        ->with('Category edit failed, already exists.', Mockery::on(
            fn (array $context) => $context['category_id'] === $category->id
        ));
});

it('logs successful edit info', function () {
    Log::spy();

    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    app(EditCategoryAction::class)->execute($user, $category, 'Renamed', editCategoryRequest());

    Log::shouldHaveReceived('info')
        ->with('Category updated.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));
});

it('clears the rate limiter after a successful edit', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $category = $user->categories()->create(['name' => 'Personal']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    foreach (range(1, 4) as $ignored) {
        app(EditCategoryAction::class)->execute($user, $category, 'Work', editCategoryRequest());
    }

    expect(RateLimiter::attempts(editCategoryRateLimitKey($user)))->toBe(4);

    app(EditCategoryAction::class)->execute($user, $category, 'Renamed', editCategoryRequest());

    expect(RateLimiter::attempts(editCategoryRateLimitKey($user)))->toBe(0);
});

it('logs the rate-limited outcome with available_in', function () {
    Log::spy();

    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $category = $user->categories()->create(['name' => 'Personal']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    foreach (range(1, 5) as $ignored) {
        app(EditCategoryAction::class)->execute($user, $category, 'Work', editCategoryRequest());
    }

    $result = app(EditCategoryAction::class)->execute($user, $category, 'Work', editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::RateLimited);
    Log::shouldHaveReceived('warning')
        ->with('Category edit rate limited.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id && $context['available_in'] > 0
        ));
});

it('trims and normalizes the new name at the domain boundary', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    $result = app(EditCategoryAction::class)->execute($user, $category, "  \u{200C}personal\u{200C}  ", editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::Updated);
    expect($category->fresh()->name)->toBe('Personal');
});

it('updates the category with exactly the guard and update queries', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear(editCategoryRateLimitKey($user));
    $this->expectsDatabaseQueryCount(2);

    $result = app(EditCategoryAction::class)->execute($user, $category, 'Renamed', editCategoryRequest());

    expect($result)->toBe(EditCategoryResult::Updated);
});

it('keeps linked tasks attached when the category is renamed', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task',
        'task_date' => now(),
        'estimated_minutes' => 30,
        'category_id' => $category->id,
    ]);
    RateLimiter::clear(editCategoryRateLimitKey($user));

    app(EditCategoryAction::class)->execute($user, $category, 'Renamed', editCategoryRequest());

    expect($task->fresh()->category_id)->toBe($category->id)
        ->and($task->fresh()->category->name)->toBe('Renamed');
});

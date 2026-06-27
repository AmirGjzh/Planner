<?php

use App\Actions\Category\DeleteCategoryAction;
use App\Enums\DeleteCategoryResult;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('deletes a category successfully', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $result = app(DeleteCategoryAction::class)->execute($user, $category);

    expect($result)->toBe(DeleteCategoryResult::Deleted);
    expect($user->categories()->where('name', 'Work')->exists())->toBeFalse();
});

it('prevents deleting a category that belongs to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $category = $user1->categories()->create(['name' => 'Work']);

    $this->expectException(HttpException::class);

    app(DeleteCategoryAction::class)->execute($user2, $category);
});

it('prevents deleting a category that has tasks', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Test task',
        'task_date' => now(),
        'estimated_minutes' => 30,
        'category_id' => $category->id,
    ]);

    $result = app(DeleteCategoryAction::class)->execute($user, $category);

    expect($result)->toBe(DeleteCategoryResult::HasTasks);
    expect($user->categories()->where('name', 'Work')->exists())->toBeTrue();
});

it('logs deletion events', function () {
    Log::spy();

    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $categoryWithTasks = $user->categories()->create(['name' => 'Personal']);
    $user->tasks()->create([
        'title' => 'Test',
        'task_date' => now(),
        'estimated_minutes' => 30,
        'category_id' => $categoryWithTasks->id,
    ]);

    app(DeleteCategoryAction::class)->execute($user, $categoryWithTasks);

    Log::shouldHaveReceived('warning')
        ->with('Category deletion failed, has tasks assigned.', Mockery::on(
            fn (array $context) => $context['name'] === 'Personal'
        ));

    app(DeleteCategoryAction::class)->execute($user, $category);

    Log::shouldHaveReceived('info')
        ->with('Category deleted.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id && $context['name'] === 'Work'
        ));
});

<?php

use App\Models\User;
use Livewire\Livewire;

it('renders the category page', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertStatus(200)
        ->assertSee('Add new category')
        ->assertSee('Your Categories')
        ->assertSee('Work');
});

it('shows empty state when no categories exist', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('No categories yet. Create one above.')
        ->assertDontSee('Tasks');
});

it('creates a new category', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('addCategory', 'Work')
        ->assertHasNoErrors();

    expect($user->categories()->where('name', 'Work')->exists())->toBeTrue();
});

it('shows error for duplicate category', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('addCategory', 'Work')
        ->assertHasErrors('new_category');
});

it('validates new category name is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('addCategory', '')
        ->assertHasErrors('new_category');
});

it('validates new category name max length', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('addCategory', str_repeat('a', 256))
        ->assertHasErrors('new_category');
});

it('edits a category name', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('editCategory', $category->id, 'Personal')
        ->assertHasNoErrors();

    expect($category->fresh()->name)->toBe('Personal');
});

it('shows error for duplicate name on edit', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $category = $user->categories()->create(['name' => 'Personal']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('editCategory', $category->id, 'Work')
        ->assertHasErrors('edit_category');
});

it('validates edit category name is required', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('editCategory', $category->id, '')
        ->assertHasErrors('edit_category');
});

it('validates edit category name max length', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('editCategory', $category->id, str_repeat('a', 256))
        ->assertHasErrors('edit_category');
});

it('deletes a category', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('deleteCategory', $category->id)
        ->assertHasNoErrors();

    expect($user->categories()->where('name', 'Work')->exists())->toBeFalse();
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

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->call('deleteCategory', $category->id)
        ->assertHasErrors('delete_category');

    expect($user->categories()->where('name', 'Work')->exists())->toBeTrue();
});

it('shows all categories on the page', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $user->categories()->create(['name' => 'Personal']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('Work')
        ->assertSee('Personal');
});

it('shows task count for each category', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('0 Tasks');
});

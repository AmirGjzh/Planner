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
        ->assertSee('My Categories')
        ->assertSee('Work');
});

it('shows empty state when no categories exist', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('No categories yet.')
        ->assertDontSee('No categories found.');
});

it('creates a new category', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', 'Work')->call('addCategory')
        ->assertHasNoErrors();

    expect($user->categories()->where('name', 'Work')->exists())->toBeTrue();
});

it('shows error for duplicate category', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', 'Work')->call('addCategory')
        ->assertSet('add_error', 'already_exists');
});

it('validates new category name is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', '')->call('addCategory')
        ->assertHasErrors('add_category');
});

it('validates new category name max length', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', str_repeat('a', 256))->call('addCategory')
        ->assertHasErrors('add_category');
});

it('edits a category name', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', 'Personal')->call('editCategory')
        ->assertHasNoErrors();

    expect($category->fresh()->name)->toBe('Personal');
});

it('shows error for duplicate name on edit', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $category = $user->categories()->create(['name' => 'Personal']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', 'Work')->call('editCategory')
        ->assertSet('edit_error', 'already_exists');
});

it('validates edit category name is required', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', '')->call('editCategory')
        ->assertHasErrors(['edit_name' => ['required']]);
});

it('validates edit category name max length', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', str_repeat('a', 256))->call('editCategory')
        ->assertHasErrors(['edit_name' => ['max']]);
});

it('deletes a category', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('deleting_id', $category->id)->call('deleteCategory')
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
        ->set('deleting_id', $category->id)->call('deleteCategory')
        ->assertSet('delete_error', 'has_tasks');

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
        ->assertSee('No Tasks');
});

it('filters categories by search term', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $user->categories()->create(['name' => 'Personal']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('search', 'Work')
        ->assertSee('Work')
        ->assertDontSee('Personal');
});

it('shows no results message when search matches nothing', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('search', 'xyz')
        ->assertSee('No categories found.');
});

it('sorts categories by name', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Beta']);
    $user->categories()->create(['name' => 'Alpha']);

    $component = Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('sort', 'name');

    $html = $component->html();
    expect(strpos($html, 'Alpha'))->toBeLessThan(strpos($html, 'Beta'));
});

it('defaults to latest sort', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSet('sort', 'latest');
});

it('shows success message after creating a category', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', 'Work')->call('addCategory')
        ->assertSet('add_success', 'created');
});

it('shows success message after editing a category', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', 'Personal')->call('editCategory')
        ->assertSet('edit_success', 'updated');
});

it('cancel add resets form state', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', 'Work')
        ->call('cancelAdd')
        ->assertSet('add_category', '')
        ->assertSet('add_error', null)
        ->assertSet('add_success', null);
});

it('cancel edit resets form state', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', 'Personal')
        ->call('cancelEdit')
        ->assertSet('edit_name', '')
        ->assertSet('edit_error', null)
        ->assertSet('edit_success', null);
});

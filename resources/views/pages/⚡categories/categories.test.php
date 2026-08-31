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
        ->assertSee('My categories')
        ->assertSee('Work');
});

it('shows empty state when no categories exist', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('No categories yet')
        ->assertDontSee('No categories found');
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

it('filters categories case-insensitively', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $user->categories()->create(['name' => 'Personal']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('search', 'wor')
        ->assertSee('Work')
        ->assertDontSee('Personal');
});

it('shows no results message when search matches nothing', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('search', 'xyz')
        ->assertSee('No categories found');
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
        ->assertDispatched('close-modal', id: 'add-category-form')
        ->assertDispatched('toast', title: __('Your category created'), variant: 'success');
});

it('shows success message after editing a category', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', 'Personal')->call('editCategory')
        ->assertDispatched('close-modal', id: 'edit-category-form')
        ->assertDispatched('toast', title: __('Your category updated'), variant: 'info');
});

it('cancel add resets form state', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', 'Work')
        ->call('cancelAdd')
        ->assertSet('add_category', '')
        ->assertSet('add_error', null);
});

it('cancel edit resets form state', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', 'Personal')
        ->call('cancelEdit')
        ->assertSet('edit_name', '')
        ->assertSet('edit_error', null);
});

it('renders the loading spinner over the category grid', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('wire:loading.delay.short', false)
        ->assertSee('animate-spin', false)
        ->assertSee('aria-label="Loading"', false);
});

it('wraps search, filter and grid in the category-content island', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('category-content', false);
});

it('links view tasks with the category filter preselected', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee(route('tasks', ['category_filter' => [$category->id]]));
});

it('dispatches close-modal and toast after creating a category', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', 'Work')->call('addCategory')
        ->assertDispatched('close-modal', id: 'add-category-form')
        ->assertDispatched('toast',
            title: __('Your category created'),
            variant: 'success',
        );
});

it('dispatches close-modal and toast after editing a category', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', 'Personal')->call('editCategory')
        ->assertDispatched('close-modal', id: 'edit-category-form')
        ->assertDispatched('toast',
            title: __('Your category updated'),
            variant: 'info',
        );
});

it('dispatches close-modal and toast after deleting a category', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('deleting_id', $category->id)->call('deleteCategory')
        ->assertDispatched('close-modal', id: 'delete-category-confirmation')
        ->assertDispatched('toast',
            title: __('Your category deleted'),
            variant: 'info',
        );
});

it('sorts categories by number of tasks', function () {
    $user = User::factory()->create();
    $few = $user->categories()->create(['name' => 'Few']);
    $many = $user->categories()->create(['name' => 'Many']);
    foreach (['Task one', 'Task two'] as $title) {
        $user->tasks()->create([
            'title' => $title,
            'task_date' => now(),
            'estimated_minutes' => 30,
            'category_id' => $many->id,
        ]);
    }

    $html = Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('sort', 'tasks')
        ->html();

    expect(strpos($html, 'Many'))->toBeLessThan(strpos($html, 'Few'));
});

it('renders pagination when there are more than six categories', function () {
    $user = User::factory()->create();
    foreach (range(1, 7) as $i) {
        $user->categories()->create(['name' => 'Category '.$i]);
    }

    $html = Livewire::actingAs($user)
        ->test('pages::categories')
        ->html();

    expect($html)->toContain('Next')
        ->toContain(__('Showing'))
        ->toContain('gotoPage(2');
    expect(substr_count($html, 'wire:key="category-'))->toBe(6);
});

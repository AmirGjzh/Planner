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

it('validates new category name max length', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', str_repeat('a', 256))->call('addCategory')
        ->assertHasErrors('add_category');
});

it('requires a name when the new category name is only whitespace', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', '   ')->call('addCategory')
        ->assertHasErrors('add_category');
});

it('trims the name before creating a category', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', "  \u{200C}Work\u{200C}  ")->call('addCategory')
        ->assertHasNoErrors();

    expect($user->categories()->where('name', 'Work')->exists())->toBeTrue();
});

it('requires a name when the new category name is only half-spaces', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', "\u{200C}\u{200C}")->call('addCategory')
        ->assertHasErrors('add_category');
});

it('detects a duplicate hidden behind a trailing half-space', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', "Work\u{200C}")->call('addCategory')
        ->assertSet('add_error', 'already_exists');
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

it('trims the name before editing a category', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', "  \u{200C}Personal\u{200C}  ")->call('editCategory')
        ->assertHasNoErrors();

    expect($category->fresh()->name)->toBe('Personal');
});

it('requires a name when the edit name is only half-spaces', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', "\u{200C}\u{200C}")->call('editCategory')
        ->assertHasErrors('edit_name');
});

it('dispatches focus-first-error when add validation fails', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('add_category', '')->call('addCategory')
        ->assertHasErrors(['add_category' => ['required']])
        ->assertDispatched('focus-first-error');
});

it('dispatches focus-first-error when edit validation fails', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)->set('edit_name', '')->call('editCategory')
        ->assertHasErrors('edit_name')
        ->assertDispatched('focus-first-error');
});

it('wires novalidate and focus handling on the add and edit forms', function () {
    $user = User::factory()->create();

    $html = Livewire::actingAs($user)
        ->test('pages::categories')
        ->html();

    expect(substr_count($html, 'novalidate'))->toBe(2)
        ->and(substr_count($html, 'focus-first-error.window'))->toBe(2)
        ->and(substr_count($html, 'aria-label="Close"'))->toBe(3);
});

it('names each card action menu after its category', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('aria-label="Work"', false);
});

it('shows the rate-limited alert when create attempts hit the bucket', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    $component = Livewire::actingAs($user)->test('pages::categories');

    foreach (range(1, 5) as $ignored) {
        $component->set('add_category', 'Work')->call('addCategory');
    }

    $component->set('add_category', 'Work')->call('addCategory')
        ->assertSet('add_error', 'rate_limited')
        ->assertSee('Too many add-category attempts!');
});

it('shows the rate-limited alert when edit attempts hit the bucket', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $category = $user->categories()->create(['name' => 'Personal']);

    $component = Livewire::actingAs($user)->test('pages::categories');

    foreach (range(1, 5) as $ignored) {
        $component->set('editing_id', $category->id)->set('edit_name', 'Work')->call('editCategory');
    }

    $component->set('editing_id', $category->id)->set('edit_name', 'Work')->call('editCategory')
        ->assertSet('edit_error', 'rate_limited')
        ->assertSee('Too many edit-category attempts!');
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

it('prevents editing a category owned by another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $category = $other->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('editing_id', $category->id)
        ->set('edit_name', 'Hacked')
        ->call('editCategory')
        ->assertNotDispatched('toast');

    expect($category->fresh()->name)->toBe('Work');
});

it('prevents deleting a category owned by another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $category = $other->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('deleting_id', $category->id)
        ->call('deleteCategory')
        ->assertNotDispatched('toast');

    expect($category->fresh())->not->toBeNull();
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

it('shows the task count for a category', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    foreach (range(1, 2) as $i) {
        $user->tasks()->create([
            'title' => 'Test task '.$i,
            'task_date' => now(),
            'estimated_minutes' => 30,
            'category_id' => $category->id,
        ]);
    }

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('2 Tasks');
});

it('shows the no-tasks state for an empty category', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

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

it('trims the search term before filtering', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);
    $user->categories()->create(['name' => 'Personal']);

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('search', '  wor  ')
        ->assertSee('Work')
        ->assertDontSee('Personal');
});

it('names the search input for assistive technology', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('aria-label="Search categories..."', false);
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

it('resets to the first page when the sort changes', function () {
    $user = User::factory()->create();

    foreach (range(1, 7) as $i) {
        $user->categories()->create(['name' => 'Category '.$i]);
    }

    // Name-sorted page 2 holds only Category 7; the sort change must return to page 1.
    Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('sort', 'name')
        ->call('gotoPage', 2)
        ->assertSee('Category 7')
        ->assertDontSee('Category 1')
        ->set('sort', 'tasks')
        ->assertSee('Category 1')
        ->assertDontSee('Category 7');
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

    // The @island directive is only observable through Livewire's fragment marker.
    Livewire::actingAs($user)
        ->test('pages::categories')
        ->assertSee('FRAGMENT:type=island|name=category-content', false);
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

it('breaks task-count ties by name', function () {
    $user = User::factory()->create();

    // Same task count (zero), created out of name order: only the
    // tiebreaker can render Alpha before Zulu.
    foreach (['Zulu', 'Alpha', 'Mike'] as $name) {
        $user->categories()->create(['name' => $name]);
    }

    $html = Livewire::actingAs($user)
        ->test('pages::categories')
        ->set('sort', 'tasks')
        ->html();

    expect(strpos($html, 'Alpha'))->toBeLessThan(strpos($html, 'Mike'))
        ->and(strpos($html, 'Mike'))->toBeLessThan(strpos($html, 'Zulu'));
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

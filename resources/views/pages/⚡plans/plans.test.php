<?php

use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

it('renders the plan page', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertStatus(200)
        ->assertSee('Add new plan')
        ->assertSee('Your Plans')
        ->assertSee('Work');
});

it('shows empty state when no plans exist', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('No plans yet. Create one above.');
});

it('creates a new plan', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('plan_name', 'Work')
        ->set('range', ['start' => '2026-01-01', 'end' => '2026-01-31'])
        ->call('addPlan')
        ->assertHasNoErrors();

    expect($user->plans()->where('name', 'Work')->exists())->toBeTrue();
});

it('shows error for duplicate plan name', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('plan_name', 'Work')
        ->set('range', ['start' => '2026-02-01', 'end' => '2026-02-28'])
        ->call('addPlan')
        ->assertHasErrors('plan_name');
});

it('validates plan name is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('plan_name', '')
        ->set('range', ['start' => '2026-01-01', 'end' => '2026-01-31'])
        ->call('addPlan')
        ->assertHasErrors('plan_name');
});

it('validates plan name max length', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('plan_name', str_repeat('a', 256))
        ->set('range', ['start' => '2026-01-01', 'end' => '2026-01-31'])
        ->call('addPlan')
        ->assertHasErrors('plan_name');
});

it('returns rate limited on create after too many attempts', function () {
    $user = User::factory()->create();
    RateLimiter::clear('create-plan:'.$user->id.'|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::actingAs($user)
            ->test('pages::plans')
            ->set('plan_name', 'Plan '.$i)
            ->set('range', ['start' => '2026-01-01', 'end' => '2026-01-31'])
            ->call('addPlan')
            ->assertHasNoErrors();
    }

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('plan_name', 'Blocked')
        ->set('range', ['start' => '2026-02-01', 'end' => '2026-02-28'])
        ->call('addPlan')
        ->assertHasErrors('plan_form');
});

it('edits a plan', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('startEditing', $plan->id)
        ->assertSet('editingPlanId', $plan->id)
        ->assertSet('editName', 'Work')
        ->assertSet('editDescription', null);

    RateLimiter::clear('edit-plan:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('startEditing', $plan->id)
        ->set('editName', 'Personal')
        ->set('editRange', ['start' => '2026-03-01', 'end' => '2026-03-31'])
        ->call('updatePlan')
        ->assertHasNoErrors();

    expect($plan->fresh()->name)->toBe('Personal');
});

it('populates edit fields via startEditing', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'description' => 'My plan', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('startEditing', $plan->id)
        ->assertSet('editingPlanId', $plan->id)
        ->assertSet('editName', 'Work')
        ->assertSet('editDescription', 'My plan');
});

it('resets edit fields via cancelEditing', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('startEditing', $plan->id)
        ->call('cancelEditing')
        ->assertSet('editingPlanId', null)
        ->assertSet('editName', '');
});

it('shows duplicate name error on edit', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $plan = $user->plans()->create(['name' => 'Personal', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('startEditing', $plan->id)
        ->set('editName', 'Work')
        ->set('editRange', ['start' => '2026-03-01', 'end' => '2026-03-31'])
        ->call('updatePlan')
        ->assertHasErrors('editName');
});

it('validates edit name is required', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('startEditing', $plan->id)
        ->set('editName', '')
        ->set('editRange', ['start' => '2026-03-01', 'end' => '2026-03-31'])
        ->call('updatePlan')
        ->assertHasErrors('editName');
});

it('validates edit name max length', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('startEditing', $plan->id)
        ->set('editName', str_repeat('a', 256))
        ->set('editRange', ['start' => '2026-03-01', 'end' => '2026-03-31'])
        ->call('updatePlan')
        ->assertHasErrors('editName');
});

it('returns rate limited on edit after too many attempts', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    RateLimiter::clear('edit-plan:'.$user->id.'|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::actingAs($user)
            ->test('pages::plans')
            ->call('startEditing', $plan->id)
            ->set('editName', 'Edit '.$i)
            ->set('editRange', ['start' => '2026-03-01', 'end' => '2026-03-31'])
            ->call('updatePlan')
            ->assertHasNoErrors();
    }

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('startEditing', $plan->id)
        ->set('editName', 'Blocked')
        ->set('editRange', ['start' => '2026-04-01', 'end' => '2026-04-30'])
        ->call('updatePlan')
        ->assertHasErrors('edit_form');
});

it('deletes a plan', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('deletePlan', $plan->id)
        ->assertHasNoErrors();

    expect($user->plans()->where('name', 'Work')->exists())->toBeFalse();
});

it('prevents deleting a plan that has tasks', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $user->tasks()->create([
        'title' => 'Test task',
        'task_date' => now(),
        'estimated_minutes' => 30,
        'category_id' => $category->id,
        'plan_id' => $plan->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->call('deletePlan', $plan->id)
        ->assertHasErrors('plan_form');

    expect($user->plans()->where('name', 'Work')->exists())->toBeTrue();
});

it('shows all plans on the page', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $user->plans()->create(['name' => 'Personal', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('Work')
        ->assertSee('Personal');
});

it('shows task count for each plan', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('No Tasks');
});

it('shows view tasks popover with task list', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $category = $user->categories()->create(['name' => 'General']);
    $user->tasks()->create(['title' => 'Task A', 'task_date' => '2026-01-15', 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id]);
    $user->tasks()->create(['title' => 'Task B', 'task_date' => '2026-01-16', 'estimated_minutes' => 45, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('View Tasks')
        ->assertSee('Task A')
        ->assertSee('Task B');
});

it('shows progress for plan with mixed done tasks', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $category = $user->categories()->create(['name' => 'General']);
    $user->tasks()->create(['title' => 'Done A', 'task_date' => '2026-01-15', 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);
    $user->tasks()->create(['title' => 'Not done B', 'task_date' => '2026-01-16', 'estimated_minutes' => 45, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('1/2 (50%) Progress');
});

it('shows 100% progress when all tasks are done', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $category = $user->categories()->create(['name' => 'General']);
    $user->tasks()->create(['title' => 'Task A', 'task_date' => '2026-01-15', 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);
    $user->tasks()->create(['title' => 'Task B', 'task_date' => '2026-01-16', 'estimated_minutes' => 45, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('2/2 (100%) Progress');
});

it('shows 0% progress when no tasks are done', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $category = $user->categories()->create(['name' => 'General']);
    $user->tasks()->create(['title' => 'Task A', 'task_date' => '2026-01-15', 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id]);
    $user->tasks()->create(['title' => 'Task B', 'task_date' => '2026-01-16', 'estimated_minutes' => 45, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('0/2 (0%) Progress');
});

it('shows empty state for plan with no tasks', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Empty Plan', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('Empty Plan')
        ->assertSee('No tasks assigned to this plan.');
});

<?php

use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

it('renders the task page', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Test task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->assertStatus(200)
        ->assertSee('Add new task')
        ->assertSee('Your Tasks')
        ->assertSee('Test task');
});

it('shows empty state when no tasks exist', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->assertSee('No Tasks yet. Create one above.');
});

it('creates a new task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test task')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasNoErrors();

    expect($user->tasks()->where('title', 'Test task')->exists())->toBeTrue();
});

it('creates a task with a plan assigned', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $plan = $user->plans()->create(['name' => 'Sprint', 'start_date' => now()->format('Y-m-d'), 'finish_date' => '2026-06-30']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Planned task')
        ->set('task_date', '2026-06-15')
        ->set('task_estimated_minutes', 60)
        ->set('task_priority', 'high')
        ->set('task_category_id', $category->id)
        ->set('task_plan_id', $plan->id)
        ->call('addTask')
        ->assertHasNoErrors();

    expect($user->tasks()->where('title', 'Planned task')->exists())->toBeTrue();
});

it('validates task title is required', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', '')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasErrors('task_title');
});

it('validates task title max length', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', str_repeat('a', 256))
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasErrors('task_title');
});

it('validates task date format', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test')
        ->set('task_date', 'invalid-date')
        ->set('task_estimated_minutes', 30)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasErrors('task_date');
});

it('validates estimated minutes is required', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 0)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasErrors('task_estimated_minutes');
});

it('validates estimated minutes max', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 1441)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasErrors('task_estimated_minutes');
});

it('validates alarm days min', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_alarm_days', -1)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasErrors('task_alarm_days');
});

it('validates alarm days max', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_alarm_days', 366)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasErrors('task_alarm_days');
});

it('validates priority is valid', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_priority', 'urgent')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasErrors('task_priority');
});

it('validates category is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_priority', 'medium')
        ->call('addTask')
        ->assertHasErrors('task_category_id');
});

it('returns rate limited on create after too many attempts', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear('create-task:'.$user->id.'|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::actingAs($user)
            ->test('pages::task-page')
            ->set('task_title', 'Task '.$i)
            ->set('task_date', now()->format('Y-m-d'))
            ->set('task_estimated_minutes', 30)
            ->set('task_priority', 'medium')
            ->set('task_category_id', $category->id)
            ->call('addTask')
            ->assertHasNoErrors();
    }

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Blocked')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasErrors('task_form');
});

it('shows invalid category error on create', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_priority', 'medium')
        ->set('task_category_id', 999)
        ->call('addTask')
        ->assertHasErrors('task_category_id');
});

it('shows invalid plan error on create', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Test')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 30)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->set('task_plan_id', 999)
        ->call('addTask')
        ->assertHasErrors('task_plan_id');
});

it('edits a task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear('edit-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('startEditing', $task->id)
        ->assertSet('editingTaskId', $task->id)
        ->assertSet('editTitle', 'Original')
        ->set('editTitle', 'Updated')
        ->set('editDate', '2026-06-15')
        ->set('editEstimatedMinutes', 60)
        ->set('editPriority', 'high')
        ->call('updateTask')
        ->assertHasNoErrors();

    expect($task->fresh()->title)->toBe('Updated');
});

it('populates edit fields via startEditing', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'description' => 'A desc', 'task_date' => now()->format('Y-m-d'),
        'estimated_minutes' => 30, 'priority' => TaskPriority::High, 'day_before_alarm' => 2,
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('startEditing', $task->id)
        ->assertSet('editingTaskId', $task->id)
        ->assertSet('editTitle', 'Test task')
        ->assertSet('editDescription', 'A desc')
        ->assertSet('editEstimatedMinutes', 30)
        ->assertSet('editAlarmDays', 2)
        ->assertSet('editPriority', 'high')
        ->assertSet('editCategoryId', $category->id);
});

it('resets edit fields via cancelEditing', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('startEditing', $task->id)
        ->call('cancelEditing')
        ->assertSet('editingTaskId', null)
        ->assertSet('editTitle', '');
});

it('validates edit title is required', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('startEditing', $task->id)
        ->set('editTitle', '')
        ->call('updateTask')
        ->assertHasErrors('editTitle');
});

it('returns rate limited on edit after too many attempts', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear('edit-task:'.$user->id.'|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::actingAs($user)
            ->test('pages::task-page')
            ->call('startEditing', $task->id)
            ->set('editTitle', 'Edit '.$i)
            ->set('editDate', now()->format('Y-m-d'))
            ->set('editEstimatedMinutes', 30)
            ->set('editPriority', 'medium')
            ->call('updateTask')
            ->assertHasNoErrors();
    }

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('startEditing', $task->id)
        ->set('editTitle', 'Blocked')
        ->set('editDate', now()->format('Y-m-d'))
        ->set('editEstimatedMinutes', 30)
        ->set('editPriority', 'medium')
        ->call('updateTask')
        ->assertHasErrors('edit_form');
});

it('shows invalid category error on edit', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('startEditing', $task->id)
        ->set('editCategoryId', 999)
        ->call('updateTask')
        ->assertHasErrors('editCategoryId');
});

it('shows invalid plan error on edit', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('startEditing', $task->id)
        ->set('editPlanId', 999)
        ->call('updateTask')
        ->assertHasErrors('editPlanId');
});

it('deletes a task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('deleteTask', $task->id)
        ->assertHasNoErrors();

    expect($user->tasks()->where('title', 'Test task')->exists())->toBeFalse();
});

it('shows all tasks on the page', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Task A', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $user->tasks()->create([
        'title' => 'Task B', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15,
        'priority' => TaskPriority::Low, 'day_before_alarm' => 1, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->assertSee('Task A')
        ->assertSee('Task B');
});

it('toggles a task from not done to done', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('toggleTask', $task->id)
        ->assertHasNoErrors();

    expect($task->fresh()->done)->toBeTrue();
});

it('toggles a task from done to not done', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id, 'done' => true,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('toggleTask', $task->id)
        ->assertHasNoErrors();

    expect($task->fresh()->done)->toBeFalse();
});

it('shows only today\'s tasks by default', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Yesterday task', 'task_date' => now()->subDay()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $user->tasks()->create([
        'title' => 'Today task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15,
        'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->assertSee('Today task')
        ->assertDontSee('Yesterday task');
});

it('applies date range filter when filter button is clicked', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $taskIn = $user->tasks()->create([
        'title' => 'In range', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $taskOut = $user->tasks()->create([
        'title' => 'Out of range', 'task_date' => now()->subMonth()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subWeek()->format('Y-m-d'), 'end' => now()->addWeek()->format('Y-m-d')])
        ->call('applyDateFilter')
        ->assertSee('In range')
        ->assertDontSee('Out of range');
});

it('filters with start date only', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Old task', 'task_date' => now()->subMonth()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $user->tasks()->create([
        'title' => 'Recent task', 'task_date' => now()->subDay()->format('Y-m-d'), 'estimated_minutes' => 15,
        'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subDays(3)->format('Y-m-d'), 'end' => now()->addDay()->format('Y-m-d')])
        ->call('applyDateFilter')
        ->assertSee('Recent task')
        ->assertDontSee('Old task');
});

it('returns null workload when no tasks exist', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toBeNull();
});

it('returns Light workload for tasks under 60 minutes', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Quick task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 30,
        'hours' => 0,
        'minutes' => 30,
        'label' => 'Light',
    ]);
});

it('returns Medium workload at 180 minute boundary', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Hour task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 180,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 180,
        'hours' => 3,
        'minutes' => 0,
        'label' => 'Medium',
    ]);
});

it('returns Heavy workload at 360 minute boundary', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Long task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 360,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 360,
        'hours' => 6,
        'minutes' => 0,
        'label' => 'Heavy',
    ]);
});

it('sums workload across multiple tasks', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Task A', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 180,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $user->tasks()->create([
        'title' => 'Task B', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 90,
        'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 270,
        'hours' => 4,
        'minutes' => 30,
        'label' => 'Medium',
    ]);
});

it('workload respects date filter', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'In range', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 180,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $user->tasks()->create([
        'title' => 'Out of range', 'task_date' => now()->subMonth()->format('Y-m-d'), 'estimated_minutes' => 360,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subWeek()->format('Y-m-d'), 'end' => now()->addWeek()->format('Y-m-d')])
        ->call('applyDateFilter');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 180,
        'hours' => 3,
        'minutes' => 0,
        'label' => 'Medium',
    ]);
});

it('renders workload on the page', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Workload test task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 210,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->assertSee('3h 30m')
        ->assertSee('Medium');
});

it('workload updates after creating a task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toBeNull();

    $component
        ->set('task_title', 'New task')
        ->set('task_date', now()->format('Y-m-d'))
        ->set('task_estimated_minutes', 180)
        ->set('task_priority', 'medium')
        ->set('task_category_id', $category->id)
        ->call('addTask')
        ->assertHasNoErrors();

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 180,
        'hours' => 3,
        'minutes' => 0,
        'label' => 'Medium',
    ]);
});

it('workload updates after deleting a task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'To delete', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 30,
        'hours' => 0,
        'minutes' => 30,
        'label' => 'Light',
    ]);

    $component->call('deleteTask', $task->id)->assertHasNoErrors();

    expect($component->instance()->workload())->toBeNull();
});

it('hides workload alert for multi-day range', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Range task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 180,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subWeek()->format('Y-m-d'), 'end' => now()->addWeek()->format('Y-m-d')])
        ->call('applyDateFilter')
        ->assertDontSee('Medium Day')
        ->assertDontSee('Rest Day');
});

it('Light workload just below Medium boundary (179 min)', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Light boundary', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 179,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 179,
        'hours' => 2,
        'minutes' => 59,
        'label' => 'Light',
    ]);
});

it('Medium workload just below Heavy boundary (359 min)', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Medium boundary', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 359,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 359,
        'hours' => 5,
        'minutes' => 59,
        'label' => 'Medium',
    ]);
});

it('filters with end date only', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Old task', 'task_date' => now()->subMonth()->format('Y-m-d'), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $user->tasks()->create([
        'title' => 'Future task', 'task_date' => now()->addWeek()->format('Y-m-d'), 'estimated_minutes' => 15,
        'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subMonth()->subDay()->format('Y-m-d'), 'end' => now()->format('Y-m-d')])
        ->call('applyDateFilter')
        ->assertSee('Old task')
        ->assertDontSee('Future task');
});

it('filters by category', function () {
    $user = User::factory()->create();
    $catA = $user->categories()->create(['name' => 'Work']);
    $catB = $user->categories()->create(['name' => 'Personal']);
    $user->tasks()->create(['title' => 'Work task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $catA->id]);
    $user->tasks()->create(['title' => 'Personal task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $catB->id]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('filterCategoryId', $catA->id)
        ->call('applyDateFilter')
        ->assertSee('Work task')
        ->assertDontSee('Personal task');
});

it('filters by plan', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $planA = $user->plans()->create(['name' => 'Sprint 1', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addMonth()->format('Y-m-d')]);
    $planB = $user->plans()->create(['name' => 'Sprint 2', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addMonth()->format('Y-m-d')]);
    $user->tasks()->create(['title' => 'Sprint 1 task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id, 'plan_id' => $planA->id]);
    $user->tasks()->create(['title' => 'Sprint 2 task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id, 'plan_id' => $planB->id]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('filterPlanId', $planA->id)
        ->call('applyDateFilter')
        ->assertSee('Sprint 1 task')
        ->assertDontSee('Sprint 2 task');
});

it('filters by done status', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create(['title' => 'Done task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id, 'done' => true]);
    $user->tasks()->create(['title' => 'Pending task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id, 'done' => false]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('filterStatus', 'done')
        ->call('applyDateFilter')
        ->assertSee('Done task')
        ->assertDontSee('Pending task');
});

it('filters by not-done status', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create(['title' => 'Done task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id, 'done' => true]);
    $user->tasks()->create(['title' => 'Pending task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id, 'done' => false]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('filterStatus', 'not_done')
        ->call('applyDateFilter')
        ->assertDontSee('Done task')
        ->assertSee('Pending task');
});

it('filters by priority', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create(['title' => 'High priority', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::High, 'day_before_alarm' => 0, 'category_id' => $category->id]);
    $user->tasks()->create(['title' => 'Low priority', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('filterPriority', 'low')
        ->call('applyDateFilter')
        ->assertDontSee('High priority')
        ->assertSee('Low priority');
});

it('combines multiple filters', function () {
    $user = User::factory()->create();
    $catA = $user->categories()->create(['name' => 'Work']);
    $catB = $user->categories()->create(['name' => 'Personal']);
    $user->tasks()->create(['title' => 'Work high', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::High, 'day_before_alarm' => 0, 'category_id' => $catA->id]);
    $user->tasks()->create(['title' => 'Work low', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $catA->id]);
    $user->tasks()->create(['title' => 'Personal high', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 45, 'priority' => TaskPriority::High, 'day_before_alarm' => 0, 'category_id' => $catB->id]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('filterCategoryId', $catA->id)
        ->set('filterPriority', 'high')
        ->call('applyDateFilter')
        ->assertSee('Work high')
        ->assertDontSee('Work low')
        ->assertDontSee('Personal high');
});

it('sorts by date', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create(['title' => 'Older', 'task_date' => now()->subWeek()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id]);
    $user->tasks()->create(['title' => 'Newer', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subMonth()->format('Y-m-d'), 'end' => now()->addDay()->format('Y-m-d')])
        ->call('applyDateFilter')
        ->set('sortByDate', true)
        ->call('applyDateFilter');

    $html = $component->html();
    expect(strpos($html, 'Older'))->toBeLessThan(strpos($html, 'Newer'));
});

it('sorts by priority', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create(['title' => 'High task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::High, 'day_before_alarm' => 0, 'category_id' => $category->id]);
    $user->tasks()->create(['title' => 'Low task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subMonth()->format('Y-m-d'), 'end' => now()->addDay()->format('Y-m-d')])
        ->call('applyDateFilter')
        ->set('sortByPriority', true)
        ->call('applyDateFilter');

    $html = $component->html();
    expect(strpos($html, 'High task'))->toBeLessThan(strpos($html, 'Low task'));
});

it('sorts by estimated minutes', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create(['title' => 'Short task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id]);
    $user->tasks()->create(['title' => 'Long task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 120, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subMonth()->format('Y-m-d'), 'end' => now()->addDay()->format('Y-m-d')])
        ->call('applyDateFilter')
        ->set('sortByEstimatedMinutes', true)
        ->call('applyDateFilter');

    $html = $component->html();
    expect(strpos($html, 'Short task'))->toBeLessThan(strpos($html, 'Long task'));
});

it('stacks multiple sort criteria', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create(['title' => 'Today low', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id]);
    $user->tasks()->create(['title' => 'Today medium', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id]);
    $user->tasks()->create(['title' => 'Yesterday high', 'task_date' => now()->subDay()->format('Y-m-d'), 'estimated_minutes' => 45, 'priority' => TaskPriority::High, 'day_before_alarm' => 0, 'category_id' => $category->id]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subMonth()->format('Y-m-d'), 'end' => now()->addDay()->format('Y-m-d')])
        ->call('applyDateFilter')
        ->set('sortByDate', true)
        ->set('sortByPriority', true)
        ->call('applyDateFilter');

    $html = $component->html();
    expect(strpos($html, 'Yesterday high'))->toBeLessThan(strpos($html, 'Today low'));
    expect(strpos($html, 'Today low'))->toBeLessThan(strpos($html, 'Today medium'));
});

it('applies filter and sort together', function () {
    $user = User::factory()->create();
    $catA = $user->categories()->create(['name' => 'Work']);
    $catB = $user->categories()->create(['name' => 'Personal']);
    $user->tasks()->create(['title' => 'Work low', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $catA->id]);
    $user->tasks()->create(['title' => 'Work high', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::High, 'day_before_alarm' => 0, 'category_id' => $catA->id]);
    $user->tasks()->create(['title' => 'Personal high', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 45, 'priority' => TaskPriority::High, 'day_before_alarm' => 0, 'category_id' => $catB->id]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('filterCategoryId', $catA->id)
        ->set('sortByPriority', true)
        ->call('applyDateFilter');

    $html = $component->html();
    expect(strpos($html, 'Work high'))->toBeLessThan(strpos($html, 'Work low'));
    $component->assertDontSee('Personal high');
});

it('defaults to date descending sort when no sort is selected', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create(['title' => 'Old task', 'task_date' => now()->subWeek()->format('Y-m-d'), 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id]);
    $user->tasks()->create(['title' => 'Recent task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 15, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subMonth()->format('Y-m-d'), 'end' => now()->addDay()->format('Y-m-d')])
        ->call('applyDateFilter');

    $html = $component->html();
    expect(strpos($html, 'Recent task'))->toBeLessThan(strpos($html, 'Old task'));
});

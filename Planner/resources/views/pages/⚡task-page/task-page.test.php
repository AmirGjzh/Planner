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

it('returns Medium workload at 120 minute boundary', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Hour task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 120,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 120,
        'hours' => 2,
        'minutes' => 0,
        'label' => 'Medium',
    ]);
});

it('returns Heavy workload at 240 minute boundary', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Long task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 240,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 240,
        'hours' => 4,
        'minutes' => 0,
        'label' => 'Heavy',
    ]);
});

it('sums workload across multiple tasks', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Task A', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 90,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $user->tasks()->create([
        'title' => 'Task B', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 45,
        'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 135,
        'hours' => 2,
        'minutes' => 15,
        'label' => 'Medium',
    ]);
});

it('workload respects date filter', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'In range', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 120,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $user->tasks()->create([
        'title' => 'Out of range', 'task_date' => now()->subMonth()->format('Y-m-d'), 'estimated_minutes' => 240,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('date_filter', ['start' => now()->subWeek()->format('Y-m-d'), 'end' => now()->addWeek()->format('Y-m-d')])
        ->call('applyDateFilter');

    expect($component->instance()->workload())->toEqual([
        'total_minutes' => 120,
        'hours' => 2,
        'minutes' => 0,
        'label' => 'Medium',
    ]);
});

it('renders workload on the page', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Workload test task', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 150,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->assertSee('2h 30m')
        ->assertSee('Medium');
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

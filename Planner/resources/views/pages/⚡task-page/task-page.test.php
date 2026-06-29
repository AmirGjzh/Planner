<?php

use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

it('renders the task page', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $user->tasks()->create([
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
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
        ->set('task_date', '2026-06-01')
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
    $plan = $user->plans()->create(['name' => 'Sprint', 'start_date' => '2026-06-01', 'finish_date' => '2026-06-30']);

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
        ->set('task_date', '2026-06-01')
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
        ->set('task_date', '2026-06-01')
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
        ->set('task_date', '2026-06-01')
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
        ->set('task_date', '2026-06-01')
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
        ->set('task_date', '2026-06-01')
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
        ->set('task_date', '2026-06-01')
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
        ->set('task_date', '2026-06-01')
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
        ->set('task_date', '2026-06-01')
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
            ->set('task_date', '2026-06-01')
            ->set('task_estimated_minutes', 30)
            ->set('task_priority', 'medium')
            ->set('task_category_id', $category->id)
            ->call('addTask')
            ->assertHasNoErrors();
    }

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->set('task_title', 'Blocked')
        ->set('task_date', '2026-06-01')
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
        ->set('task_date', '2026-06-01')
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
        ->set('task_date', '2026-06-01')
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
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
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
        'title' => 'Test task', 'description' => 'A desc', 'task_date' => '2026-06-01',
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
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
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
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
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
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear('edit-task:'.$user->id.'|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::actingAs($user)
            ->test('pages::task-page')
            ->call('startEditing', $task->id)
            ->set('editTitle', 'Edit '.$i)
            ->set('editDate', '2026-06-01')
            ->set('editEstimatedMinutes', 30)
            ->set('editPriority', 'medium')
            ->call('updateTask')
            ->assertHasNoErrors();
    }

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->call('startEditing', $task->id)
        ->set('editTitle', 'Blocked')
        ->set('editDate', '2026-06-01')
        ->set('editEstimatedMinutes', 30)
        ->set('editPriority', 'medium')
        ->call('updateTask')
        ->assertHasErrors('edit_form');
});

it('shows invalid category error on edit', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
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
        'title' => 'Original', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
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
        'title' => 'Test task', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
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
        'title' => 'Task A', 'task_date' => '2026-06-01', 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    $user->tasks()->create([
        'title' => 'Task B', 'task_date' => '2026-06-02', 'estimated_minutes' => 15,
        'priority' => TaskPriority::Low, 'day_before_alarm' => 1, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::task-page')
        ->assertSee('Task A')
        ->assertSee('Task B');
});

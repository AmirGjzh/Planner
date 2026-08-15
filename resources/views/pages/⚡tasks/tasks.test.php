<?php

use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

function taskDate(string $offset = '0 days'): string
{
    return now()->modify($offset)->format('Y-m-d');
}

function makeTask(User $user, string $title, array $overrides = []): void
{
    $category = $user->categories()->where('name', 'Work')->first()
        ?? $user->categories()->create(['name' => 'Work']);

    $user->tasks()->create(array_merge([
        'title' => $title,
        'task_date' => taskDate(),
        'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium,
        'day_before_alarm' => 0,
        'category_id' => $category->id,
    ], $overrides));
}

function wideRange(): array
{
    return [
        'start' => now()->subYears(2)->format('Y-m-d'),
        'end' => now()->addYears(2)->format('Y-m-d'),
    ];
}

it('renders the task page', function () {
    $user = User::factory()->create();
    makeTask($user, 'Test task');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertStatus(200)
        ->assertSee('Add new task')
        ->assertSee('My Tasks')
        ->assertSee('Test task');
});

it('shows empty state when no tasks exist', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSee('No tasks yet.');
});

it('shows active, overdue and completed status badges', function () {
    $user = User::factory()->create();
    makeTask($user, 'Active task');
    makeTask($user, 'Overdue task', ['task_date' => taskDate('-1 days')]);
    makeTask($user, 'Completed task', ['done' => true]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('range_filter', wideRange())
        ->assertSee('Active task')
        ->assertSee('Overdue task')
        ->assertSee('Completed task');
});

it('creates a new task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear('create-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test task')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', $category->id)
        ->set('add_plan_id', null)
        ->call('addTask')
        ->assertHasNoErrors()
        ->assertSet('add_success', 'created');

    expect($user->tasks()->where('title', 'Test task')->exists())->toBeTrue();
});

it('creates a task with a plan assigned', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $plan = $user->plans()->create(['name' => 'Sprint', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    RateLimiter::clear('create-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Planned task')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 60)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'high')
        ->set('add_category_id', $category->id)
        ->set('add_plan_id', $plan->id)
        ->call('addTask')
        ->assertHasNoErrors();

    expect($user->tasks()->where('title', 'Planned task')->exists())->toBeTrue();
});

it('validates task title is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', '')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', 1)
        ->call('addTask')
        ->assertHasErrors('add_title');
});

it('validates task title max length', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', str_repeat('a', 256))
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', 1)
        ->call('addTask')
        ->assertHasErrors('add_title');
});

it('validates task date format', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test')
        ->set('add_date', 'invalid-date')
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', 1)
        ->call('addTask')
        ->assertHasErrors('add_date');
});

it('validates estimated minutes is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 0)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', 1)
        ->call('addTask')
        ->assertHasErrors('add_estimated_minutes');
});

it('validates estimated minutes max', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 1441)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', 1)
        ->call('addTask')
        ->assertHasErrors('add_estimated_minutes');
});

it('validates alarm days min', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', -1)
        ->set('add_priority', 'medium')
        ->set('add_category_id', 1)
        ->call('addTask')
        ->assertHasErrors('add_alarm_days');
});

it('validates alarm days max', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 366)
        ->set('add_priority', 'medium')
        ->set('add_category_id', 1)
        ->call('addTask')
        ->assertHasErrors('add_alarm_days');
});

it('validates priority is valid', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'urgent')
        ->set('add_category_id', 1)
        ->call('addTask')
        ->assertHasErrors('add_priority');
});

it('validates category is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', null)
        ->call('addTask')
        ->assertHasErrors('add_category_id');
});

it('returns rate limited on create after too many attempts', function () {
    $user = User::factory()->create();
    RateLimiter::clear('create-task:'.$user->id.'|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::actingAs($user)
            ->test('pages::tasks')
            ->set('add_title', 'Attempt '.$i)
            ->set('add_date', taskDate())
            ->set('add_estimated_minutes', 30)
            ->set('add_alarm_days', 0)
            ->set('add_priority', 'medium')
            ->set('add_category_id', 999)
            ->call('addTask')
            ->assertSet('add_error', 'invalid_category');
    }

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Blocked')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', 999)
        ->call('addTask')
        ->assertSet('add_error', 'rate_limited');
});

it('shows invalid category error on create', function () {
    $user = User::factory()->create();
    RateLimiter::clear('create-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', 999)
        ->call('addTask')
        ->assertSet('add_error', 'invalid_category');
});

it('shows invalid plan error on create', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    RateLimiter::clear('create-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'Test')
        ->set('add_date', taskDate())
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', $category->id)
        ->set('add_plan_id', 999)
        ->call('addTask')
        ->assertSet('add_error', 'invalid_plan');
});

it('edits a task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear('edit-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('editing_id', $task->id)
        ->set('edit_title', 'Updated')
        ->set('edit_description', 'New desc')
        ->set('edit_date', taskDate())
        ->set('edit_estimated_minutes', 60)
        ->set('edit_alarm_days', 2)
        ->set('edit_priority', 'high')
        ->set('edit_category_id', $category->id)
        ->set('edit_plan_id', null)
        ->call('editTask')
        ->assertHasNoErrors()
        ->assertSet('edit_success', 'updated');

    expect($task->fresh()->title)->toBe('Updated');
    expect($task->fresh()->description)->toBe('New desc');
    expect($task->fresh()->estimated_minutes)->toBe(60);
    expect($task->fresh()->priority)->toBe(TaskPriority::High);
});

it('validates edit title is required', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('editing_id', $task->id)
        ->set('edit_title', '')
        ->set('edit_date', taskDate())
        ->set('edit_estimated_minutes', 30)
        ->set('edit_priority', 'medium')
        ->set('edit_category_id', $category->id)
        ->call('editTask')
        ->assertHasErrors('edit_title');
});

it('returns rate limited on edit after too many attempts', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear('edit-task:'.$user->id.'|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::actingAs($user)
            ->test('pages::tasks')
            ->set('editing_id', $task->id)
            ->set('edit_title', 'Attempt '.$i)
            ->set('edit_date', taskDate())
            ->set('edit_estimated_minutes', 30)
            ->set('edit_priority', 'medium')
            ->set('edit_category_id', 999)
            ->call('editTask')
            ->assertSet('edit_error', 'invalid_category');
    }

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('editing_id', $task->id)
        ->set('edit_title', 'Blocked')
        ->set('edit_date', taskDate())
        ->set('edit_estimated_minutes', 30)
        ->set('edit_priority', 'medium')
        ->set('edit_category_id', 999)
        ->call('editTask')
        ->assertSet('edit_error', 'rate_limited');
});

it('shows invalid category error on edit', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear('edit-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('editing_id', $task->id)
        ->set('edit_title', 'Test')
        ->set('edit_date', taskDate())
        ->set('edit_estimated_minutes', 30)
        ->set('edit_priority', 'medium')
        ->set('edit_category_id', 999)
        ->call('editTask')
        ->assertSet('edit_error', 'invalid_category');
});

it('shows invalid plan error on edit', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Original', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear('edit-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('editing_id', $task->id)
        ->set('edit_title', 'Test')
        ->set('edit_date', taskDate())
        ->set('edit_estimated_minutes', 30)
        ->set('edit_priority', 'medium')
        ->set('edit_category_id', $category->id)
        ->set('edit_plan_id', 999)
        ->call('editTask')
        ->assertSet('edit_error', 'invalid_plan');
});

it('deletes a task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('deleting_id', $task->id)
        ->call('deleteTask')
        ->assertDispatched('close-modal', id: 'delete-task-confirmation');

    expect($user->tasks()->where('title', 'Test task')->exists())->toBeFalse();
});

it('completes a task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear('toggle-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('completing_id', $task->id)
        ->call('completeTask')
        ->assertSet('complete_error', null)
        ->assertDispatched('close-modal', id: 'complete-task-confirmation');

    expect($task->fresh()->done)->toBeTrue();
});

it('reopens a completed task', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id, 'done' => true,
    ]);
    RateLimiter::clear('toggle-task:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('reopening_id', $task->id)
        ->call('reopenTask')
        ->assertDispatched('close-modal', id: 'reopen-task-confirmation');

    expect($task->fresh()->done)->toBeFalse();
});

it('returns rate limited on complete after too many toggles', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);
    RateLimiter::clear('toggle-task:'.$user->id.'|127.0.0.1');

    foreach (range(1, 20) as $i) {
        Livewire::actingAs($user)
            ->test('pages::tasks')
            ->set('completing_id', $task->id)
            ->call('completeTask')
            ->assertSet('complete_error', null);
    }

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('completing_id', $task->id)
        ->call('completeTask')
        ->assertSet('complete_error', 'rate_limited');
});

it('returns rate limited on reopen after too many toggles', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $task = $user->tasks()->create([
        'title' => 'Test task', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id, 'done' => true,
    ]);
    RateLimiter::clear('toggle-task:'.$user->id.'|127.0.0.1');

    foreach (range(1, 20) as $i) {
        Livewire::actingAs($user)
            ->test('pages::tasks')
            ->set('reopening_id', $task->id)
            ->call('reopenTask')
            ->assertSet('reopen_error', null);
    }

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('reopening_id', $task->id)
        ->call('reopenTask')
        ->assertSet('reopen_error', 'rate_limited');
});

it('prevents completing a task owned by another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $category = $other->categories()->create(['name' => 'Work']);
    $task = $other->tasks()->create([
        'title' => 'Private', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $this->expectException(ModelNotFoundException::class);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('completing_id', $task->id)
        ->call('completeTask');
});

it('prevents editing a task owned by another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $category = $other->categories()->create(['name' => 'Work']);
    $task = $other->tasks()->create([
        'title' => 'Private', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $this->expectException(ModelNotFoundException::class);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('editing_id', $task->id)
        ->set('edit_title', 'Hacked')
        ->set('edit_date', taskDate())
        ->set('edit_estimated_minutes', 30)
        ->set('edit_priority', 'medium')
        ->set('edit_category_id', $category->id)
        ->call('editTask');
});

it('prevents deleting a task owned by another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $category = $other->categories()->create(['name' => 'Work']);
    $task = $other->tasks()->create([
        'title' => 'Private', 'task_date' => taskDate(), 'estimated_minutes' => 30,
        'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'category_id' => $category->id,
    ]);

    $this->expectException(ModelNotFoundException::class);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('deleting_id', $task->id)
        ->call('deleteTask');
});

it('shows only today\'s tasks by default', function () {
    $user = User::factory()->create();
    makeTask($user, 'Today task');
    makeTask($user, 'Past task', ['task_date' => taskDate('-3 days')]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSet('range_filter', [
            'start' => now()->today()->format('Y-m-d'),
            'end' => now()->today()->format('Y-m-d'),
        ])
        ->assertSee('Today task')
        ->assertDontSee('Past task');
});

it('applies a date range filter', function () {
    $user = User::factory()->create();
    makeTask($user, 'In range', ['task_date' => taskDate('-5 days')]);
    makeTask($user, 'Out of range', ['task_date' => taskDate('+5 days')]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('range_filter', [
            'start' => taskDate('-7 days'),
            'end' => taskDate('-3 days'),
        ])
        ->assertSee('In range')
        ->assertDontSee('Out of range');
});

it('filters with start date only', function () {
    $user = User::factory()->create();
    makeTask($user, 'After start', ['task_date' => taskDate()]);
    makeTask($user, 'Before start', ['task_date' => taskDate('-3 days')]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('range_filter', ['start' => taskDate(), 'end' => null])
        ->assertSee('After start')
        ->assertDontSee('Before start');
});

it('filters with end date only', function () {
    $user = User::factory()->create();
    makeTask($user, 'Before end', ['task_date' => taskDate('-1 days')]);
    makeTask($user, 'After end', ['task_date' => taskDate('+3 days')]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('range_filter', ['start' => null, 'end' => taskDate()])
        ->assertSee('Before end')
        ->assertDontSee('After end');
});

it('defaults the sort to state', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSet('sort', 'state');
});

it('renders the loading spinner over the task grid', function () {
    $user = User::factory()->create();
    makeTask($user, 'Task');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSee('wire:loading.delay.short', false)
        ->assertSee('animate-spin', false)
        ->assertSee('aria-label="Loading"', false);
});

it('wraps search, filters and grid in the tasks-content island', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSee('tasks-content', false);
});

it('scopes category, plan and date-range filters to the tasks-content island', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSee("island: 'tasks-content'", false);
});

it('sorts by state putting active, overdue then completed first', function () {
    $user = User::factory()->create();
    makeTask($user, 'Completed task', ['done' => true]);
    makeTask($user, 'Overdue task', ['task_date' => taskDate('-1 days')]);
    makeTask($user, 'Active task');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('range_filter', wideRange())
        ->set('sort', 'state')
        ->assertSet('sort', 'state')
        ->assertSeeInOrder(['Active task', 'Overdue task', 'Completed task']);
});

it('sorts by load descending estimated minutes', function () {
    $user = User::factory()->create();
    makeTask($user, 'Short task', ['estimated_minutes' => 30]);
    makeTask($user, 'Long task', ['estimated_minutes' => 1200]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('sort', 'load')
        ->assertSeeInOrder(['Long task', 'Short task']);
});

it('sorts by date', function () {
    $user = User::factory()->create();
    makeTask($user, 'Later task', ['task_date' => taskDate('+2 days')]);
    makeTask($user, 'Earlier task', ['task_date' => taskDate('-2 days')]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('range_filter', wideRange())
        ->set('sort', 'date')
        ->assertSeeInOrder(['Earlier task', 'Later task']);
});

it('sorts by priority high first', function () {
    $user = User::factory()->create();
    makeTask($user, 'Low task', ['priority' => TaskPriority::Low]);
    makeTask($user, 'High task', ['priority' => TaskPriority::High]);
    makeTask($user, 'Medium task', ['priority' => TaskPriority::Medium]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('sort', 'priority')
        ->assertSeeInOrder(['High task', 'Medium task', 'Low task']);
});

it('sorts by estimated time descending', function () {
    $user = User::factory()->create();
    makeTask($user, 'Small task', ['estimated_minutes' => 30]);
    makeTask($user, 'Big task', ['estimated_minutes' => 300]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('sort', 'estimated')
        ->assertSeeInOrder(['Big task', 'Small task']);
});

it('sorts by latest first', function () {
    $user = User::factory()->create();
    makeTask($user, 'Newer task');
    makeTask($user, 'Older task');

    $user->tasks()->where('title', 'Older task')->update(['created_at' => now()->subDay()]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('sort', 'latest')
        ->assertSeeInOrder(['Newer task', 'Older task']);
});

it('filters tasks by active status', function () {
    $user = User::factory()->create();
    makeTask($user, 'Active task');
    makeTask($user, 'Completed task', ['done' => true]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('status_filter', 'active')
        ->assertSee('Active task')
        ->assertDontSee('Completed task');
});

it('filters tasks by completed status', function () {
    $user = User::factory()->create();
    makeTask($user, 'Active task');
    makeTask($user, 'Completed task', ['done' => true]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('status_filter', 'completed')
        ->assertSee('Completed task')
        ->assertDontSee('Active task');
});

it('filters tasks by overdue status', function () {
    $user = User::factory()->create();
    makeTask($user, 'Active task');
    makeTask($user, 'Overdue task', ['task_date' => taskDate('-1 days')]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('range_filter', wideRange())
        ->set('status_filter', 'overdue')
        ->assertSee('Overdue task')
        ->assertDontSee('Active task');
});

it('defaults the status filter to all and shows every task', function () {
    $user = User::factory()->create();
    makeTask($user, 'Active task');
    makeTask($user, 'Overdue task', ['task_date' => taskDate('-1 days')]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('range_filter', wideRange())
        ->assertSee('Active task')
        ->assertSee('Overdue task');
});

it('applies the status filter from the URL query string', function () {
    $user = User::factory()->create();
    makeTask($user, 'Active task');
    makeTask($user, 'Completed task', ['done' => true]);

    Livewire::actingAs($user)
        ->test('pages::tasks', ['status_filter' => 'completed'])
        ->assertSee('Completed task')
        ->assertDontSee('Active task');
});

it('searches tasks by title', function () {
    $user = User::factory()->create();
    makeTask($user, 'Alpha task');
    makeTask($user, 'Bravo task');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('search', 'Alpha')
        ->assertSee('Alpha task')
        ->assertDontSee('Bravo task');
});

it('shows no results message when search matches nothing', function () {
    $user = User::factory()->create();
    makeTask($user, 'Alpha task');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('search', 'Missing')
        ->assertSee('No tasks found.')
        ->assertDontSee('Alpha task');
});

it('renders the sort dropdown options', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSee('Sort')
        ->assertSee('State')
        ->assertSee('Load')
        ->assertSee('Latest')
        ->assertSee('Date')
        ->assertSee('Priority');
});

it('renders the filter dropdown options', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSee('Filter Task')
        ->assertSee('All')
        ->assertSee('Active')
        ->assertSee('Completed')
        ->assertSee('Overdue');
});

it('renders category and plan filter dropdowns with their options', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $plan = $user->plans()->create(['name' => 'Sprint', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSee('Category')
        ->assertSee('Plan')
        ->assertSee('Sprint');
});

it('shows tasks from all categories when no category is selected', function () {
    $user = User::factory()->create();
    $personal = $user->categories()->create(['name' => 'Personal']);
    $work = $user->categories()->create(['name' => 'Work']);
    makeTask($user, 'Personal task', ['category_id' => $personal->id]);
    makeTask($user, 'Work task', ['category_id' => $work->id]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSee('Personal task')
        ->assertSee('Work task');
});

it('filters tasks by a single category', function () {
    $user = User::factory()->create();
    $personal = $user->categories()->create(['name' => 'Personal']);
    $work = $user->categories()->create(['name' => 'Work']);
    makeTask($user, 'Personal task', ['category_id' => $personal->id]);
    makeTask($user, 'Work task', ['category_id' => $work->id]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('category_filter', [$work->id])
        ->assertSee('Work task')
        ->assertDontSee('Personal task');
});

it('filters tasks by multiple categories at once', function () {
    $user = User::factory()->create();
    $personal = $user->categories()->create(['name' => 'Personal']);
    $work = $user->categories()->create(['name' => 'Work']);
    $study = $user->categories()->create(['name' => 'Study']);
    makeTask($user, 'Personal task', ['category_id' => $personal->id]);
    makeTask($user, 'Work task', ['category_id' => $work->id]);
    makeTask($user, 'Study task', ['category_id' => $study->id]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('category_filter', [$work->id, $personal->id])
        ->assertSee('Work task')
        ->assertSee('Personal task')
        ->assertDontSee('Study task');
});

it('shows tasks from all plans when no plan is selected', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $planA = $user->plans()->create(['name' => 'Plan A', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    $planB = $user->plans()->create(['name' => 'Plan B', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    makeTask($user, 'Plan A task', ['category_id' => $category->id, 'plan_id' => $planA->id]);
    makeTask($user, 'Plan B task', ['category_id' => $category->id, 'plan_id' => $planB->id]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->assertSee('Plan A task')
        ->assertSee('Plan B task');
});

it('filters tasks by a single plan', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $planA = $user->plans()->create(['name' => 'Plan A', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    $planB = $user->plans()->create(['name' => 'Plan B', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    makeTask($user, 'Plan A task', ['category_id' => $category->id, 'plan_id' => $planA->id]);
    makeTask($user, 'Plan B task', ['category_id' => $category->id, 'plan_id' => $planB->id]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('plan_filter', [$planA->id])
        ->assertSee('Plan A task')
        ->assertDontSee('Plan B task');
});

it('filters tasks by multiple plans at once', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);
    $planA = $user->plans()->create(['name' => 'Plan A', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    $planB = $user->plans()->create(['name' => 'Plan B', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    $planC = $user->plans()->create(['name' => 'Plan C', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    makeTask($user, 'Plan A task', ['category_id' => $category->id, 'plan_id' => $planA->id]);
    makeTask($user, 'Plan B task', ['category_id' => $category->id, 'plan_id' => $planB->id]);
    makeTask($user, 'Plan C task', ['category_id' => $category->id, 'plan_id' => $planC->id]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('plan_filter', [$planA->id, $planB->id])
        ->assertSee('Plan A task')
        ->assertSee('Plan B task')
        ->assertDontSee('Plan C task');
});

it('combines category and plan filters', function () {
    $user = User::factory()->create();
    $personal = $user->categories()->create(['name' => 'Personal']);
    $work = $user->categories()->create(['name' => 'Work']);
    $planA = $user->plans()->create(['name' => 'Plan A', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    $planB = $user->plans()->create(['name' => 'Plan B', 'start_date' => '2026-01-01', 'finish_date' => '2026-12-31']);
    makeTask($user, 'Matching task', ['category_id' => $work->id, 'plan_id' => $planA->id]);
    makeTask($user, 'Wrong plan task', ['category_id' => $work->id, 'plan_id' => $planB->id]);
    makeTask($user, 'Wrong category task', ['category_id' => $personal->id, 'plan_id' => $planA->id]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('category_filter', [$work->id])
        ->set('plan_filter', [$planA->id])
        ->assertSee('Matching task')
        ->assertDontSee('Wrong plan task')
        ->assertDontSee('Wrong category task');
});

it('shows no results message when category filter matches nothing', function () {
    $user = User::factory()->create();
    makeTask($user, 'Work task');

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('category_filter', [999])
        ->assertSee('No tasks found.')
        ->assertDontSee('Work task');
});

it('hydrates the category filter from the categories query string', function () {
    $user = User::factory()->create();
    $work = $user->categories()->create(['name' => 'Work']);
    $personal = $user->categories()->create(['name' => 'Personal']);
    makeTask($user, 'Work task', ['category_id' => $work->id]);
    makeTask($user, 'Personal task', ['category_id' => $personal->id]);
    makeTask($user, 'Old work task', ['category_id' => $work->id, 'task_date' => taskDate('-10 days'), 'title' => 'Old Work Task']);

    $this->actingAs($user)
        ->get(route('tasks', ['category_filter' => [$work->id]]))
        ->assertOk()
        ->assertSee('Work task')
        ->assertDontSee('Personal task')
        ->assertSee('Old Work Task');
});

it('hydrates the plan filter from the plans query string', function () {
    $user = User::factory()->create();
    $alpha = $user->plans()->create(['name' => 'Alpha', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(5)->format('Y-m-d')]);
    $beta = $user->plans()->create(['name' => 'Beta', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(5)->format('Y-m-d')]);
    makeTask($user, 'Alpha task', ['plan_id' => $alpha->id]);
    makeTask($user, 'Beta task', ['plan_id' => $beta->id]);
    makeTask($user, 'Old alpha task', ['plan_id' => $alpha->id, 'task_date' => taskDate('-10 days'), 'title' => 'Old Alpha Task']);

    $this->actingAs($user)
        ->get(route('tasks', ['plan_filter' => [$alpha->id]]))
        ->assertOk()
        ->assertSee('Alpha task')
        ->assertDontSee('Beta task')
        ->assertSee('Old Alpha Task');
});

it('preselects the plan and opens the add modal via the add_plan query param', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Sprint', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(5)->format('Y-m-d')]);

    Livewire::withQueryParams(['add_plan' => $plan->id])
        ->actingAs($user)
        ->test('pages::tasks')
        ->assertSet('add_plan_id', $plan->id)
        ->assertDispatched('open-modal', id: 'add-task-form');
});

it('ignores an add_plan param for a plan owned by another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $plan = $other->plans()->create(['name' => 'Private', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(5)->format('Y-m-d')]);

    Livewire::withQueryParams(['add_plan' => $plan->id])
        ->actingAs($user)
        ->test('pages::tasks')
        ->assertSet('add_plan_id', null)
        ->assertNotDispatched('open-modal', id: 'add-task-form');
});

<?php

use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

function planWideRange(): array
{
    return [
        'start' => now()->subYears(2)->format('Y-m-d'),
        'end' => now()->addYears(2)->format('Y-m-d'),
    ];
}

it('renders the plan page', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('range_filter', planWideRange())
        ->assertStatus(200)
        ->assertSee('Add new plan')
        ->assertSee('My Plans')
        ->assertSee('Work');
});

it('shows empty state when no plans exist', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('No plans yet.');
});

it('shows active, overdue and completed status badges', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Active', 'start_date' => now()->subDay()->format('Y-m-d'), 'finish_date' => now()->addDays(5)->format('Y-m-d')]);
    $user->plans()->create(['name' => 'Overdue', 'start_date' => '2026-01-01', 'finish_date' => now()->subDay()->format('Y-m-d')]);
    $user->plans()->create(['name' => 'Completed', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('Active')
        ->assertSee('Overdue')
        ->assertSee('Completed');
});

it('creates a new plan', function () {
    $user = User::factory()->create();
    RateLimiter::clear('create-plan:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('add_name', 'Work')
        ->set('add_range', ['start' => '2026-01-01', 'end' => '2026-01-31'])
        ->call('addPlan')
        ->assertHasNoErrors()
        ->assertSet('add_success', 'created');

    expect($user->plans()->where('name', 'Work')->exists())->toBeTrue();
});

it('shows error for duplicate plan name', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    RateLimiter::clear('create-plan:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('add_name', 'Work')
        ->set('add_range', ['start' => '2026-02-01', 'end' => '2026-02-28'])
        ->call('addPlan')
        ->assertSet('add_error', 'already_exists');
});

it('validates plan name is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('add_name', '')
        ->set('add_range', ['start' => '2026-01-01', 'end' => '2026-01-31'])
        ->call('addPlan')
        ->assertHasErrors('add_name');
});

it('validates plan name max length', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('add_name', str_repeat('a', 256))
        ->set('add_range', ['start' => '2026-01-01', 'end' => '2026-01-31'])
        ->call('addPlan')
        ->assertHasErrors('add_name');
});

it('validates date range is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('add_name', 'Work')
        ->call('addPlan')
        ->assertHasErrors('add_range');
});

it('validates end date is after start date', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('add_name', 'Work')
        ->set('add_range', ['start' => '2026-02-28', 'end' => '2026-02-01'])
        ->call('addPlan')
        ->assertHasErrors('add_range.end');
});

it('returns rate limited on create after too many attempts', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    RateLimiter::clear('create-plan:'.$user->id.'|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::actingAs($user)
            ->test('pages::plans')
            ->set('add_name', 'Work')
            ->set('add_range', ['start' => '2026-01-01', 'end' => '2026-01-31'])
            ->call('addPlan')
            ->assertSet('add_error', 'already_exists');
    }

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('add_name', 'Blocked')
        ->set('add_range', ['start' => '2026-02-01', 'end' => '2026-02-28'])
        ->call('addPlan')
        ->assertSet('add_error', 'rate_limited');
});

it('edits a plan', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    RateLimiter::clear('edit-plan:'.$user->id.'|127.0.0.1');

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('editing_id', $plan->id)
        ->set('edit_name', 'Personal')
        ->set('edit_range', ['start' => '2026-03-01', 'end' => '2026-03-31'])
        ->call('editPlan')
        ->assertHasNoErrors()
        ->assertSet('edit_success', 'updated');

    expect($plan->fresh()->name)->toBe('Personal');
});

it('shows duplicate name error on edit', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $plan = $user->plans()->create(['name' => 'Personal', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('editing_id', $plan->id)
        ->set('edit_name', 'Work')
        ->set('edit_range', ['start' => '2026-03-01', 'end' => '2026-03-31'])
        ->call('editPlan')
        ->assertSet('edit_error', 'already_exists');
});

it('validates edit name is required', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('editing_id', $plan->id)
        ->set('edit_name', '')
        ->set('edit_range', ['start' => '2026-03-01', 'end' => '2026-03-31'])
        ->call('editPlan')
        ->assertHasErrors('edit_name');
});

it('validates edit name max length', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('editing_id', $plan->id)
        ->set('edit_name', str_repeat('a', 256))
        ->set('edit_range', ['start' => '2026-03-01', 'end' => '2026-03-31'])
        ->call('editPlan')
        ->assertHasErrors('edit_name');
});

it('returns rate limited on edit after too many attempts', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $user->plans()->create(['name' => 'Existing', 'start_date' => '2026-02-01', 'finish_date' => '2026-02-28']);
    RateLimiter::clear('edit-plan:'.$user->id.'|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::actingAs($user)
            ->test('pages::plans')
            ->set('editing_id', $plan->id)
            ->set('edit_name', 'Existing')
            ->set('edit_range', ['start' => '2026-01-01', 'end' => '2026-01-31'])
            ->call('editPlan')
            ->assertSet('edit_error', 'already_exists');
    }

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('editing_id', $plan->id)
        ->set('edit_name', 'Blocked')
        ->set('edit_range', ['start' => '2026-02-01', 'end' => '2026-02-28'])
        ->call('editPlan')
        ->assertSet('edit_error', 'rate_limited');
});

it('deletes a plan', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('deleting_id', $plan->id)
        ->call('deletePlan')
        ->assertDispatched('close-modal', id: 'delete-plan-confirmation');

    expect($user->plans()->where('name', 'Work')->exists())->toBeFalse();
});

it('prevents deleting a plan that has tasks', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $user->tasks()->create([
        'title' => 'Test task',
        'task_date' => now(),
        'estimated_minutes' => 30,
        'plan_id' => $plan->id,
        'category_id' => $user->categories()->create(['name' => 'General'])->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('deleting_id', $plan->id)
        ->call('deletePlan')
        ->assertSet('delete_error', 'has_tasks');

    expect($user->plans()->where('name', 'Work')->exists())->toBeTrue();
});

it('completes a plan when all tasks are done', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $category = $user->categories()->create(['name' => 'General']);
    $user->tasks()->create(['title' => 'A', 'task_date' => '2026-01-15', 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);
    $user->tasks()->create(['title' => 'B', 'task_date' => '2026-01-16', 'estimated_minutes' => 45, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('completing_id', $plan->id)
        ->call('completePlan')
        ->assertSet('complete_error', null)
        ->assertDispatched('close-modal', id: 'complete-plan-confirmation');

    expect($plan->fresh()->done)->toBeTrue();
});

it('completes a plan with no tasks', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('completing_id', $plan->id)
        ->call('completePlan')
        ->assertDispatched('close-modal', id: 'complete-plan-confirmation');

    expect($plan->fresh()->done)->toBeTrue();
});

it('blocks completing a plan with undone tasks', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);
    $category = $user->categories()->create(['name' => 'General']);
    $user->tasks()->create(['title' => 'A', 'task_date' => '2026-01-15', 'estimated_minutes' => 30, 'priority' => TaskPriority::Medium, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id, 'done' => true]);
    $user->tasks()->create(['title' => 'B', 'task_date' => '2026-01-16', 'estimated_minutes' => 45, 'priority' => TaskPriority::Low, 'day_before_alarm' => 0, 'plan_id' => $plan->id, 'category_id' => $category->id]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('completing_id', $plan->id)
        ->call('completePlan')
        ->assertSet('complete_error', 'has_undone_tasks');

    expect($plan->fresh()->done)->toBeFalse();
});

it('reopens a completed plan', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('reopening_id', $plan->id)
        ->call('reopenPlan')
        ->assertDispatched('close-modal', id: 'reopen-plan-confirmation');

    expect($plan->fresh()->done)->toBeFalse();
});

it('prevents completing a plan owned by another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $plan = $other->plans()->create(['name' => 'Private', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    $this->expectException(ModelNotFoundException::class);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('completing_id', $plan->id)
        ->call('completePlan');
});

it('prevents reopening a plan owned by another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $plan = $other->plans()->create(['name' => 'Private', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    $this->expectException(ModelNotFoundException::class);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('reopening_id', $plan->id)
        ->call('reopenPlan');
});

it('filters plans by active status', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Alpha', 'start_date' => '2026-01-01', 'finish_date' => now()->addDays(5)->format('Y-m-d')]);
    $user->plans()->create(['name' => 'Bravo', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('status_filter', 'active')
        ->assertSee('Alpha')
        ->assertDontSee('Bravo');
});

it('filters plans by completed status', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Alpha', 'start_date' => '2026-01-01', 'finish_date' => now()->addDays(5)->format('Y-m-d')]);
    $user->plans()->create(['name' => 'Bravo', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('range_filter', planWideRange())
        ->set('status_filter', 'completed')
        ->assertSee('Bravo')
        ->assertDontSee('Alpha');
});

it('filters plans by overdue status', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Alpha', 'start_date' => '2026-01-01', 'finish_date' => now()->subDays(3)->format('Y-m-d')]);
    $user->plans()->create(['name' => 'Bravo', 'start_date' => '2026-01-01', 'finish_date' => now()->addDays(5)->format('Y-m-d')]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('status_filter', 'overdue')
        ->assertSee('Alpha')
        ->assertDontSee('Bravo');
});

it('defaults the status filter to all and shows every plan', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Alpha', 'start_date' => '2026-01-01', 'finish_date' => now()->addDays(5)->format('Y-m-d')]);
    $user->plans()->create(['name' => 'Bravo', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('range_filter', planWideRange())
        ->assertSee('Alpha')
        ->assertSee('Bravo');
});

it('applies the status filter from the URL query string', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Alpha', 'start_date' => '2026-01-01', 'finish_date' => now()->addDays(5)->format('Y-m-d')]);
    $user->plans()->create(['name' => 'Bravo', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31', 'done' => true]);

    Livewire::actingAs($user)
        ->test('pages::plans', ['status_filter' => 'completed'])
        ->set('range_filter', planWideRange())
        ->assertSee('Bravo')
        ->assertDontSee('Alpha');
});

it('renders the filter dropdown options', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('Filter Plan')
        ->assertSee('All')
        ->assertSee('Active')
        ->assertSee('Completed')
        ->assertSee('Overdue');
});

it('sorts plans by deadline', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Later', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(30)->format('Y-m-d')]);
    $user->plans()->create(['name' => 'Sooner', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(2)->format('Y-m-d')]);

    $component = Livewire::actingAs($user)
        ->test('pages::plans')
        ->set('range_filter', planWideRange())
        ->set('sort', 'deadline');

    $html = $component->html();
    expect(strpos($html, 'Sooner'))->toBeLessThan(strpos($html, 'Later'));
});

it('defaults to state sort', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSet('sort', 'state');
});

it('renders the loading spinner over the plan grid', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(5)->format('Y-m-d')]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('wire:loading.delay.short', false)
        ->assertSee('animate-spin', false)
        ->assertSee('aria-label="Loading"', false);
});

it('wraps the plan content in the plans-content island', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee('plans-content', false);
});

it('scopes the date-range calendar to the plans-content island', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee("island: 'plans-content'", false);
});

it('links view tasks with the plan filter preselected', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Roadmap', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(5)->format('Y-m-d')]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee(route('tasks', ['plan_filter' => [$plan->id]]));
});

it('links add task with the plan preselected too', function () {
    $user = User::factory()->create();
    $plan = $user->plans()->create(['name' => 'Roadmap', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(5)->format('Y-m-d')]);

    Livewire::actingAs($user)
        ->test('pages::plans')
        ->assertSee(route('tasks', ['plan_filter' => [$plan->id], 'add_plan' => $plan->id]));
});

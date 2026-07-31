<?php

use App\Models\User;
use Carbon\Carbon;
use Livewire\Livewire;

it('renders the report page', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::report-page')
        ->assertStatus(200)
        ->assertSee('Reports')
        ->assertSee('Tasks Created')
        ->assertSee('Tasks Completed')
        ->assertSee('Completion Rate')
        ->assertSee('Overdue Tasks');
});

it('shows correct stats for a date range with tasks', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $user->tasks()->create([
        'title' => 'Task 1', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'category_id' => $category->id, 'done' => true,
    ]);
    $user->tasks()->create([
        'title' => 'Task 2', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 20,
        'category_id' => $category->id, 'done' => false,
    ]);
    $user->tasks()->create([
        'title' => 'Task 3', 'task_date' => Carbon::yesterday()->format('Y-m-d'), 'estimated_minutes' => 10,
        'category_id' => $category->id, 'done' => false,
    ]);

    $start = Carbon::yesterday()->format('Y-m-d');
    $end = now()->format('Y-m-d');

    Livewire::actingAs($user)
        ->test('pages::report-page')
        ->set('date_filter', ['start' => $start, 'end' => $end])
        ->call('applyDateFilter')
        ->assertSee('3')
        ->assertSee('1');
});

it('shows zero stats when no tasks exist', function () {
    $user = User::factory()->create();

    $start = now()->startOfMonth()->format('Y-m-d');
    $end = now()->endOfMonth()->format('Y-m-d');

    Livewire::actingAs($user)
        ->test('pages::report-page')
        ->set('date_filter', ['start' => $start, 'end' => $end])
        ->call('applyDateFilter')
        ->assertSee('0')
        ->assertSee('0%');
});

it('calculates completion rate correctly', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $user->tasks()->create([
        'title' => 'Done 1', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 10,
        'category_id' => $category->id, 'done' => true,
    ]);
    $user->tasks()->create([
        'title' => 'Done 2', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 10,
        'category_id' => $category->id, 'done' => true,
    ]);
    $user->tasks()->create([
        'title' => 'Not done', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 10,
        'category_id' => $category->id, 'done' => false,
    ]);

    $start = now()->format('Y-m-d');
    $end = now()->format('Y-m-d');

    Livewire::actingAs($user)
        ->test('pages::report-page')
        ->set('date_filter', ['start' => $start, 'end' => $end])
        ->call('applyDateFilter')
        ->assertSee('67');
});

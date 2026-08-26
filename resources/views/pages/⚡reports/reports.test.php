<?php

use App\Models\User;
use Carbon\Carbon;
use Livewire\Livewire;

it('renders the reports page with all sections', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::reports')
        ->assertStatus(200)
        ->assertSee('My reports')
        ->assertSee('This week')
        ->assertSee('Last week')
        ->assertSee('This month')
        ->assertSee('Last month')
        ->assertSee('Custom')
        ->assertSee('Tasks Summary')
        ->assertSee('Plans Summary')
        ->assertSee('Workload Chart')
        ->assertSee('Completed')
        ->assertSee('Remaining');
});

it('defaults to the current week range', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test('pages::reports');

    $range = $component->instance()->range_filter;

    expect($range['start'])->toBe(now()->startOfWeek(Carbon::SUNDAY)->toDateString());
    expect($range['end'])->toBe(now()->startOfWeek(Carbon::SUNDAY)->addDays(6)->toDateString());
});

it('switches preset ranges through the preset buttons', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test('pages::reports');

    $component->call('selectPreset', 'last_week');
    expect($component->instance()->preset)->toBe('last_week');
    expect($component->instance()->range_filter['start'])
        ->toBe(now()->subWeek()->startOfWeek(Carbon::SUNDAY)->toDateString());

    $component->call('selectPreset', 'this_month');
    expect($component->instance()->preset)->toBe('this_month');
    expect($component->instance()->range_filter['start'])->toBe(now()->startOfMonth()->toDateString());
    expect($component->instance()->range_filter['end'])->toBe(now()->endOfMonth()->toDateString());

    $component->call('selectPreset', 'last_month');
    expect($component->instance()->preset)->toBe('last_month');
    expect($component->instance()->range_filter['start'])->toBe(now()->subMonth()->startOfMonth()->toDateString());
    expect($component->instance()->range_filter['end'])->toBe(now()->subMonth()->endOfMonth()->toDateString());
});

it('ignores unknown presets', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::reports')
        ->call('selectPreset', 'yesterday')
        ->assertSet('preset', 'this_week');
});

it('shows the calendar only for the custom preset', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::reports')
        ->assertDontSeeHtml('data-slot="calendar"')
        ->call('selectPreset', 'custom')
        ->assertSet('preset', 'custom')
        ->assertSeeHtml('data-slot="calendar"');
});

it('updates stats live when the range changes without a generate step', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $user->tasks()->create([
        'title' => 'In range', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 60,
        'category_id' => $category->id, 'done' => true,
    ]);

    $component = Livewire::actingAs($user)->test('pages::reports');

    expect($component->instance()->stats['total_tasks'])->toBe(1);

    $component->set('range_filter', [
        'start' => now()->subMonth()->startOfMonth()->toDateString(),
        'end' => now()->subMonth()->endOfMonth()->toDateString(),
    ]);

    expect($component->instance()->stats['total_tasks'])->toBe(0);
    expect($component->instance()->chart)->toBe([]);
});

it('calculates task stats for the selected range', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $user->tasks()->create([
        'title' => 'Done', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'category_id' => $category->id, 'done' => true,
    ]);
    $user->tasks()->create([
        'title' => 'Pending', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 20,
        'category_id' => $category->id, 'done' => false,
    ]);
    $user->tasks()->create([
        'title' => 'Old done', 'task_date' => Carbon::yesterday()->format('Y-m-d'), 'estimated_minutes' => 10,
        'category_id' => $category->id, 'done' => true,
    ]);
    $user->tasks()->create([
        'title' => 'Out of range', 'task_date' => now()->addMonth()->format('Y-m-d'), 'estimated_minutes' => 999,
        'category_id' => $category->id, 'done' => false,
    ]);

    $start = Carbon::yesterday()->toDateString();
    $end = now()->toDateString();

    $component = Livewire::actingAs($user)
        ->test('pages::reports')
        ->set('range_filter', ['start' => $start, 'end' => $end]);

    $stats = $component->instance()->stats;

    expect($stats['total_tasks'])->toBe(3);
    expect($stats['completed_tasks'])->toBe(2);
    expect($stats['completion_rate'])->toBe(67.0);
    expect($stats['estimated_time'])->toBe('1h');
});

it('counts plans that overlap the selected range', function () {
    $user = User::factory()->create();

    $user->plans()->create([
        'name' => 'Fully inside', 'start_date' => now()->format('Y-m-d'), 'finish_date' => now()->addDays(2)->format('Y-m-d'),
        'done' => true,
    ]);
    $user->plans()->create([
        'name' => 'Starts before, ends inside', 'start_date' => now()->subDays(3)->format('Y-m-d'),
        'finish_date' => now()->format('Y-m-d'), 'done' => false,
    ]);
    $user->plans()->create([
        'name' => 'Starts inside, ends after', 'start_date' => now()->format('Y-m-d'),
        'finish_date' => now()->addDays(10)->format('Y-m-d'), 'done' => false,
    ]);
    $user->plans()->create([
        'name' => 'Entirely before', 'start_date' => now()->subMonth()->format('Y-m-d'),
        'finish_date' => now()->subMonth()->addDays(2)->format('Y-m-d'), 'done' => false,
    ]);
    $user->plans()->create([
        'name' => 'Entirely after', 'start_date' => now()->addMonth()->format('Y-m-d'),
        'finish_date' => now()->addMonth()->addDays(2)->format('Y-m-d'), 'done' => false,
    ]);

    $start = now()->startOfWeek(Carbon::SUNDAY)->toDateString();
    $end = now()->startOfWeek(Carbon::SUNDAY)->addDays(6)->toDateString();

    $component = Livewire::actingAs($user)->test('pages::reports');

    $stats = $component->instance()->stats;

    expect($stats['total_plans'])->toBe(3);
    expect($stats['completed_plans'])->toBe(1);
});

it('shows zero stats when the range has no data', function () {
    $user = User::factory()->create();

    $start = now()->subMonth()->startOfMonth()->toDateString();
    $end = now()->subMonth()->endOfMonth()->toDateString();

    $component = Livewire::actingAs($user)
        ->test('pages::reports')
        ->set('range_filter', ['start' => $start, 'end' => $end]);

    $stats = $component->instance()->stats;

    expect($stats['total_tasks'])->toBe(0);
    expect($stats['completed_tasks'])->toBe(0);
    expect($stats['completion_rate'])->toBe(0);
    expect($stats['estimated_time'])->toBe('0m');
    expect($stats['total_plans'])->toBe(0);
});

it('builds a per-day chart from estimated minutes, only for days with tasks', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $user->tasks()->create([
        'title' => 'Done today', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 90,
        'category_id' => $category->id, 'done' => true,
    ]);
    $user->tasks()->create([
        'title' => 'Pending today', 'task_date' => now()->format('Y-m-d'), 'estimated_minutes' => 30,
        'category_id' => $category->id, 'done' => false,
    ]);
    $user->tasks()->create([
        'title' => 'Done yesterday', 'task_date' => Carbon::yesterday()->format('Y-m-d'), 'estimated_minutes' => 45,
        'category_id' => $category->id, 'done' => true,
    ]);

    $start = Carbon::yesterday()->toDateString();
    $end = now()->toDateString();

    $component = Livewire::actingAs($user)
        ->test('pages::reports')
        ->set('range_filter', ['start' => $start, 'end' => $end]);

    $chart = $component->instance()->chart;

    expect($chart)->toHaveCount(2);
    expect($chart[0]['label'])->toBe(Carbon::parse($start)->format('M j'));
    expect($chart[0]['completed'])->toBe(45);
    expect($chart[0]['remaining'])->toBe(0);
    expect($chart[1]['completed'])->toBe(90);
    expect($chart[1]['remaining'])->toBe(30);

    $component->assertSeeHtml('border-t border-dashed');
});

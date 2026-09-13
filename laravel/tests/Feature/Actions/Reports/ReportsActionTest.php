<?php

use App\Actions\Reports\ReportsAction;
use App\Models\User;
use Carbon\Carbon;

function reportTask(User $user, string $title, array $overrides = []): void
{
    $user->tasks()->create(array_merge([
        'title' => $title,
        'task_date' => now()->format('Y-m-d'),
        'estimated_minutes' => 30,
        'day_before_alarm' => 0,
        'category_id' => $user->categories()->firstOrCreate(['name' => 'Work'])->id,
        'done' => false,
    ], $overrides));
}

function reportPlan(User $user, string $name, string $startDate, string $finishDate, bool $done = false): void
{
    $user->plans()->create([
        'name' => $name,
        'start_date' => $startDate,
        'finish_date' => $finishDate,
        'done' => $done,
    ]);
}

it('sums the task stats for the selected range', function () {
    $user = User::factory()->create();
    reportTask($user, 'Done', ['estimated_minutes' => 30, 'done' => true]);
    reportTask($user, 'Pending', ['estimated_minutes' => 20]);
    reportTask($user, 'Out of range', ['task_date' => now()->addMonth()->format('Y-m-d'), 'estimated_minutes' => 999]);

    $summary = app(ReportsAction::class)->summary($user, now()->toDateString(), now()->toDateString());

    expect($summary['total_tasks'])->toBe(2);
    expect($summary['completed_tasks'])->toBe(1);
    expect($summary['estimated_time'])->toBe('50m');
});

it('computes the completion rate rounded to a percentage', function () {
    $user = User::factory()->create();
    reportTask($user, 'A', ['done' => true]);
    reportTask($user, 'B', ['done' => true]);
    reportTask($user, 'C');

    $summary = app(ReportsAction::class)->summary($user, now()->toDateString(), now()->toDateString());

    expect($summary['completion_rate'])->toBe(67.0);
});

it('counts plans that overlap the selected range', function () {
    $user = User::factory()->create();
    reportPlan($user, 'Inside', now()->toDateString(), now()->addDays(2)->toDateString(), done: true);
    reportPlan($user, 'Straddles start', now()->subDays(3)->toDateString(), now()->toDateString());
    reportPlan($user, 'Straddles end', now()->toDateString(), now()->addDays(10)->toDateString());
    reportPlan($user, 'Before', now()->subMonth()->toDateString(), now()->subMonth()->addDays(2)->toDateString());
    reportPlan($user, 'After', now()->addMonth()->toDateString(), now()->addMonth()->addDays(2)->toDateString());

    $summary = app(ReportsAction::class)->summary($user, now()->toDateString(), now()->addDays(7)->toDateString());

    expect($summary['total_plans'])->toBe(3);
    expect($summary['completed_plans'])->toBe(1);
});

it('returns zero stats when the range has no data', function () {
    $user = User::factory()->create();

    $summary = app(ReportsAction::class)->summary($user, now()->subMonth()->toDateString(), now()->subMonth()->toDateString());

    expect($summary['total_tasks'])->toBe(0);
    expect($summary['completed_tasks'])->toBe(0);
    expect($summary['completion_rate'])->toBe(0);
    expect($summary['estimated_time'])->toBe('0m');
    expect($summary['total_plans'])->toBe(0);
    expect($summary['completed_plans'])->toBe(0);
});

it('scopes the summary to the authenticated user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    reportTask($other, 'Private', ['done' => true]);
    reportPlan($other, 'Private plan', now()->toDateString(), now()->addDays(2)->toDateString(), done: true);

    $summary = app(ReportsAction::class)->summary($user, now()->toDateString(), now()->addDays(2)->toDateString());

    expect($summary['total_tasks'])->toBe(0);
    expect($summary['total_plans'])->toBe(0);
});

it('accepts an inverted range and still counts the tasks', function () {
    $user = User::factory()->create();
    reportTask($user, 'In between', ['task_date' => now()->toDateString()]);

    $summary = app(ReportsAction::class)->summary($user, now()->addDays(2)->toDateString(), now()->subDays(2)->toDateString());

    expect($summary['total_tasks'])->toBe(1);
});

it('builds a per-day chart with completed and remaining minutes', function () {
    $user = User::factory()->create();
    reportTask($user, 'Done today', ['estimated_minutes' => 90, 'done' => true]);
    reportTask($user, 'Pending today', ['estimated_minutes' => 30]);
    reportTask($user, 'Done yesterday', ['task_date' => Carbon::yesterday()->format('Y-m-d'), 'estimated_minutes' => 45, 'done' => true]);

    $chart = app(ReportsAction::class)->chart($user, Carbon::yesterday()->toDateString(), now()->toDateString());

    expect($chart)->toHaveCount(2);
    expect($chart[0]['label'])->toBe(Carbon::yesterday()->format('M j'));
    expect($chart[0]['completed'])->toBe(45);
    expect($chart[0]['remaining'])->toBe(0);
    expect($chart[1]['completed'])->toBe(90);
    expect($chart[1]['remaining'])->toBe(30);
});

it('orders chart days ascending', function () {
    $user = User::factory()->create();
    reportTask($user, 'Today', ['task_date' => now()->toDateString()]);
    reportTask($user, 'Yesterday', ['task_date' => Carbon::yesterday()->format('Y-m-d')]);

    $chart = app(ReportsAction::class)->chart($user, Carbon::yesterday()->toDateString(), now()->toDateString());

    expect($chart[0]['label'])->toBe(Carbon::yesterday()->format('M j'));
    expect($chart[1]['label'])->toBe(now()->format('M j'));
});

it('only includes days that have tasks in the chart', function () {
    $user = User::factory()->create();
    reportTask($user, 'Only day', ['task_date' => now()->toDateString()]);

    $chart = app(ReportsAction::class)->chart($user, now()->subDays(2)->toDateString(), now()->toDateString());

    expect($chart)->toHaveCount(1);
});

it('scopes the chart to the authenticated user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    reportTask($other, 'Private', ['estimated_minutes' => 999]);

    $chart = app(ReportsAction::class)->chart($user, now()->toDateString(), now()->toDateString());

    expect($chart)->toBe([]);
});

it('accepts an inverted range in the chart', function () {
    $user = User::factory()->create();
    reportTask($user, 'In between', ['task_date' => now()->toDateString()]);

    $chart = app(ReportsAction::class)->chart($user, now()->addDays(2)->toDateString(), now()->subDays(2)->toDateString());

    expect($chart)->toHaveCount(1);
    expect($chart[0]['label'])->toBe(now()->format('M j'));
});

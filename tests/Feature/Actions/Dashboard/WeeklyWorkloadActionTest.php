<?php

use App\Actions\Dashboard\WeeklyWorkloadAction;
use App\Enums\WorkloadLevel;
use App\Models\User;
use App\Support\Jalali;
use App\Support\Minutes;
use Carbon\Carbon;

function workloadTask(User $user, int $minutes, string $date, bool $done = false): void
{
    $user->tasks()->create([
        'title' => 'Task '.fake()->unique()->numberBetween(1, 9999),
        'task_date' => $date,
        'estimated_minutes' => $minutes,
        'day_before_alarm' => 0,
        'category_id' => $user->categories()->firstOrCreate(['name' => 'Work'])->id,
        'done' => $done,
    ]);
}

function workloadTodayCell(array $week): array
{
    return collect($week)->first(fn (array $day) => $day['is_today']);
}

it('returns seven day cells starting on Sunday', function () {
    $user = User::factory()->create();
    $start = now()->startOfWeek(Carbon::SUNDAY);

    $week = app(WeeklyWorkloadAction::class)->execute($user);

    expect($week)->toHaveCount(7);
    expect($week[0]['day'])->toBe($start->format('D'));
    expect($week[6]['date'])->toBe($start->copy()->addDays(6)->format('M j'));
});

it('marks the current day', function () {
    $user = User::factory()->create();

    $week = app(WeeklyWorkloadAction::class)->execute($user);

    expect(workloadTodayCell($week)['is_today'])->toBeTrue();
    expect(collect($week)->filter(fn (array $day) => $day['is_today'])->count())->toBe(1);
});

it('marks past days', function () {
    $user = User::factory()->create();

    $week = app(WeeklyWorkloadAction::class)->execute($user);

    expect(collect($week)->filter(fn (array $day) => $day['past'])->count())
        ->toBe((int) now()->startOfWeek(Carbon::SUNDAY)->diffInDays(now()->startOfDay()));
});

it('sums estimated minutes for each day including completed tasks', function () {
    $user = User::factory()->create();
    $today = now()->toDateString();
    workloadTask($user, 100, $today);
    workloadTask($user, 20, $today, done: true);

    $week = app(WeeklyWorkloadAction::class)->execute($user);

    expect(workloadTodayCell($week)['minutes'])->toBe(120);
    expect(workloadTodayCell($week)['formatted'])->toBe('2h');
});

it('returns zero minutes for days without tasks', function () {
    $user = User::factory()->create();

    $week = app(WeeklyWorkloadAction::class)->execute($user);

    expect(workloadTodayCell($week)['minutes'])->toBe(0);
    expect(workloadTodayCell($week)['formatted'])->toBe('0m');
});

it('excludes tasks outside the current week', function () {
    $user = User::factory()->create();
    workloadTask($user, 999, now()->addMonth()->toDateString());

    $week = app(WeeklyWorkloadAction::class)->execute($user);

    expect(collect($week)->sum(fn (array $day) => $day['minutes']))->toBe(0);
});

it('scopes the workload to the authenticated user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    workloadTask($other, 999, now()->toDateString());

    $week = app(WeeklyWorkloadAction::class)->execute($user);

    expect(collect($week)->sum(fn (array $day) => $day['minutes']))->toBe(0);
});

it('maps each day to a workload level', function () {
    $user = User::factory()->create();
    $today = now()->toDateString();
    workloadTask($user, 121, $today);

    $week = app(WeeklyWorkloadAction::class)->execute($user);

    expect(workloadTodayCell($week)['level'])->toBe(WorkloadLevel::Moderate->value);
});

it('formats minutes as hours and minutes', function () {
    expect(Minutes::format(0))->toBe('0m');
    expect(Minutes::format(30))->toBe('30m');
    expect(Minutes::format(120))->toBe('2h');
    expect(Minutes::format(150))->toBe('2h 30m');
});

it('formats minutes in persian when locale is fa', function () {
    app()->setLocale('fa');

    expect(Minutes::format(0))->toBe('۰ دقیقه');
    expect(Minutes::format(30))->toBe('۳۰ دقیقه');
    expect(Minutes::format(120))->toBe('۲ ساعت');
    expect(Minutes::format(150))->toBe('۲ ساعت و ۳۰ دقیقه');
});

it('renders the day and date in Jalali when locale is fa', function () {
    app()->setLocale('fa');

    $user = User::factory()->create();
    $week = app(WeeklyWorkloadAction::class)->execute($user);

    $start = now()->startOfWeek(Carbon::SATURDAY);

    expect($week[0]['day'])->toBe(Jalali::format($start, 'EEEE'));
    expect($week[6]['date'])->toBe(Jalali::format($start->copy()->addDays(6), 'd MMM'));
});

it('starts the week on Saturday when locale is fa', function () {
    app()->setLocale('fa');

    $user = User::factory()->create();
    $week = app(WeeklyWorkloadAction::class)->execute($user);

    $saturday = now()->startOfWeek(Carbon::SATURDAY);

    expect($week[0]['day'])->toBe(Jalali::format($saturday, 'EEEE'));
    expect($week[0]['day'])->toBe('شنبه');
});

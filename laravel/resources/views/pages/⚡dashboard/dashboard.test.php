<?php

use App\Enums\WorkloadLevel;
use App\Models\User;
use App\Support\Jalali;
use Livewire\Livewire;

function dashboardTask(User $user, string $title, array $overrides = []): void
{
    $user->tasks()->create(array_merge([
        'title' => $title,
        'task_date' => now()->format('Y-m-d'),
        'estimated_minutes' => 30,
        'day_before_alarm' => 0,
        'category_id' => $user->categories()->firstOrCreate(['name' => 'Work'])->id,
    ], $overrides));
}

function dashboardWeek(array $week): array
{
    return collect($week)->first(fn (array $day) => $day['is_today']);
}

it('renders the dashboard with attention heading and view all link', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertStatus(200)
        ->assertSee('Tasks needing attention')
        ->assertSee("This week's workload")
        ->assertSeeHtml('href="'.route('tasks').'"');
});

it('shows tasks within notification window', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Upcoming task', ['task_date' => now()->addDays(2)->format('Y-m-d'), 'day_before_alarm' => 3]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('Upcoming task');
});

it('hides tasks outside notification window', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Far future task', ['task_date' => now()->addDays(5)->format('Y-m-d'), 'day_before_alarm' => 1]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertDontSee('Far future task');
});

it('hides future tasks when the alarm is set for the day itself', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Tomorrow task', ['task_date' => now()->addDay()->format('Y-m-d'), 'day_before_alarm' => 0]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertDontSee('Tomorrow task');
});

it('excludes completed tasks from the attention list', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Done task', ['done' => true]);
    dashboardTask($user, 'Pending task');

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('Pending task')
        ->assertDontSee('Done task');
});

it('shows empty state when no attention tasks', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('No tasks need your attention right now.');
});

it('shows priority, overdue and due-in badges', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Overdue task', ['task_date' => now()->subDays(2)->format('Y-m-d'), 'priority' => 'high']);
    dashboardTask($user, 'Upcoming task', ['task_date' => now()->addDays(2)->format('Y-m-d'), 'day_before_alarm' => 3, 'priority' => 'low']);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('High')
        ->assertSee('Low')
        ->assertSee('Overdue')
        ->assertSee('in 2 days');
});

it('labels tasks due today and tomorrow', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Due today task');
    dashboardTask($user, 'Due tomorrow task', ['task_date' => now()->addDay()->format('Y-m-d'), 'day_before_alarm' => 1]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('Due today')
        ->assertSee('Due tomorrow');
});

it('pluralizes the overdue label', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'One day late', ['task_date' => now()->subDay()->format('Y-m-d')]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('1 day ago');
});

it('links each attention card to the tasks page with the task title as search', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Searchable task', ['task_date' => now()->addDays(2)->format('Y-m-d'), 'day_before_alarm' => 3]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSeeHtml('href="'.route('tasks', ['search' => 'Searchable task']).'"');
});

it('scopes the attention list to the authenticated user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    dashboardTask($other, 'Private task');

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertDontSee('Private task');
});

it('shows the current week with today marked', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test('pages::dashboard');
    $nonToday = collect($component->instance()->week)->firstWhere('is_today', false);

    $component->assertSee('Today')
        ->assertSee($nonToday['day']);
});

it('renders localized strings and Jalali dates in fa', function () {
    $user = User::factory()->create(['locale' => 'fa']);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('کارهای نیازمند توجه')
        ->assertSee('حجم کار این هفته')
        ->assertSee('امروز')
        ->assertSee(Jalali::format(now(), 'd MMM'));
});

it('sums estimated minutes for each day in the workload grid', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Task one', ['estimated_minutes' => 100]);
    dashboardTask($user, 'Task two', ['estimated_minutes' => 20]);

    $component = Livewire::actingAs($user)->test('pages::dashboard');

    $today = dashboardWeek($component->instance()->week);
    expect($today['minutes'])->toBe(120);
    expect($today['formatted'])->toBe('2h');
    expect($today['level'])->toBe(WorkloadLevel::Light->value);

    $component->assertSee('2h')->assertSee('Light');
});

it('counts completed tasks toward the workload total', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Done task', ['estimated_minutes' => 100, 'done' => true]);
    dashboardTask($user, 'Pending task', ['estimated_minutes' => 20]);

    $week = Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->instance()
        ->week;

    expect(dashboardWeek($week)['minutes'])->toBe(120);
});

it('shows formatted hours and minutes in the workload grid', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Long task', ['estimated_minutes' => 150]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('2h 30m');
});

it('excludes tasks outside the current week from the workload grid', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Next month task', ['task_date' => now()->addMonth()->format('Y-m-d'), 'estimated_minutes' => 999]);

    $week = Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->instance()
        ->week;

    expect(collect($week)->sum(fn (array $day) => $day['minutes']))->toBe(0);
});

it('scopes the workload grid to the authenticated user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    dashboardTask($other, 'Private task', ['estimated_minutes' => 999]);

    $week = Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->instance()
        ->week;

    expect(dashboardWeek($week)['minutes'])->toBe(0);
});

it('maps workload levels from the grid data', function () {
    $user = User::factory()->create();

    $week = Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->instance()
        ->week;

    expect($week)->toHaveCount(7);

    foreach ($week as $day) {
        expect($day)->toHaveKeys(['day', 'date', 'is_today', 'past', 'minutes', 'formatted', 'level']);
        expect(in_array($day['level'], array_column(WorkloadLevel::cases(), 'value'), true))->toBeTrue();
    }
});

it('maps minutes to the correct workload level thresholds', function () {
    expect(WorkloadLevel::forMinutes(0)->value)->toBe('none');
    expect(WorkloadLevel::forMinutes(120)->value)->toBe('light');
    expect(WorkloadLevel::forMinutes(121)->value)->toBe('moderate');
    expect(WorkloadLevel::forMinutes(240)->value)->toBe('moderate');
    expect(WorkloadLevel::forMinutes(241)->value)->toBe('heavy');
    expect(WorkloadLevel::forMinutes(360)->value)->toBe('heavy');
    expect(WorkloadLevel::forMinutes(361)->value)->toBe('very_heavy');
});

it('renders the attention count badge with the number of in-window tasks', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'First attention task');
    dashboardTask($user, 'Second attention task');

    $html = Livewire::actingAs($user)->test('pages::dashboard')->html();

    expect($html)->toMatch('/mine-btn-primary[^>]*>\s*2\s*</');
});

it('renders every dashboard count with Persian digits in fa', function () {
    $user = User::factory()->create(['locale' => 'fa']);
    dashboardTask($user, 'کار گذشته', ['task_date' => now()->subDays(2)->format('Y-m-d')]);
    dashboardTask($user, 'کار آینده', ['task_date' => now()->addDays(3)->format('Y-m-d'), 'day_before_alarm' => 4]);

    $html = Livewire::actingAs($user)->test('pages::dashboard')->html();

    expect($html)->toMatch('/mine-btn-primary[^>]*>\s*۲\s*</')
        ->and($html)->toContain('۲ روز پیش')
        ->and($html)->toContain('۳ روز دیگه');
});

it('keeps a single h1 on the page', function () {
    $user = User::factory()->create();

    $html = Livewire::actingAs($user)->test('pages::dashboard')->html();

    expect(substr_count($html, '<h1'))->toBe(1);
});

it('orders attention cards by date with a deterministic id tiebreak', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Later task', ['task_date' => now()->addDays(3)->format('Y-m-d'), 'day_before_alarm' => 3]);
    dashboardTask($user, 'Sooner task', ['task_date' => now()->format('Y-m-d')]);
    dashboardTask($user, 'Tie task', ['task_date' => now()->format('Y-m-d')]);

    $html = Livewire::actingAs($user)->test('pages::dashboard')->html();

    expect(strpos($html, 'Sooner task'))->toBeLessThan(strpos($html, 'Tie task'))
        ->and(strpos($html, 'Tie task'))->toBeLessThan(strpos($html, 'Later task'));
});

it('pins the canonical root variant line and the card-title overflow guard', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Contract task');

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('flex max-w-7xl flex-1 flex-col px-4 pt-4 pb-4 sm:px-8 sm:pb-6 md:px-16 md:pb-14', false)
        ->assertSee('truncate min-w-0', false);
});

it('runs exactly two queries on render', function () {
    $user = User::factory()->create();
    dashboardTask($user, 'Counted task');

    $this->expectsDatabaseQueryCount(2);

    Livewire::actingAs($user)->test('pages::dashboard')->assertStatus(200);
});

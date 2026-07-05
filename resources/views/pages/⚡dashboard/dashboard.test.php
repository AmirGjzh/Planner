<?php

use App\Models\User;
use Livewire\Livewire;

it('renders the dashboard with upcoming tasks heading', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertStatus(200)
        ->assertSee('Upcoming Tasks')
        ->assertSee('View All Tasks');
});

it('shows tasks within notification window', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $user->tasks()->create([
        'title' => 'Upcoming task',
        'task_date' => now()->addDays(2)->format('Y-m-d'),
        'estimated_minutes' => 30,
        'day_before_alarm' => 3,
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('Upcoming task');
});

it('hides tasks outside notification window', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $user->tasks()->create([
        'title' => 'Far future task',
        'task_date' => now()->addDays(5)->format('Y-m-d'),
        'estimated_minutes' => 30,
        'day_before_alarm' => 1,
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertDontSee('Far future task');
});

it('shows empty state when no upcoming tasks', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('No upcoming tasks.');
});

it('shows done and not done badges', function () {
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Work']);

    $user->tasks()->create([
        'title' => 'Done task',
        'task_date' => now()->addDay()->format('Y-m-d'),
        'estimated_minutes' => 15,
        'day_before_alarm' => 2,
        'category_id' => $category->id,
        'done' => true,
    ]);

    $user->tasks()->create([
        'title' => 'Not done task',
        'task_date' => now()->addDay()->format('Y-m-d'),
        'estimated_minutes' => 20,
        'day_before_alarm' => 2,
        'category_id' => $category->id,
        'done' => false,
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSee('Done')
        ->assertSee('Not Done');
});

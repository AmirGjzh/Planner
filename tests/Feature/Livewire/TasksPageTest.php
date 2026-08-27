<?php

use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;

it('renders the no-plan option with an empty value', function () {
    $user = User::factory()->create();
    Category::factory()->create(['user_id' => $user->id]);

    $html = $this->actingAs($user)
        ->get('/tasks')
        ->assertSuccessful()
        ->getContent();

    expect($html)
        ->toContain(__('No plan'))
        ->and(preg_match('/data-value="[^"]*"\s+data-label="'.preg_quote(__('No plan'), '/').'"/', $html, $m))->toBe(1);

    preg_match('/data-value="([^"]*)"/', $m[0], $value);
    expect($value[1])->toBe('');
});

it('creates a task without a plan', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('add_title', 'No plan task')
        ->set('add_date', now()->format('Y-m-d'))
        ->set('add_estimated_minutes', 30)
        ->set('add_alarm_days', 0)
        ->set('add_priority', 'medium')
        ->set('add_category_id', $category->id)
        ->set('add_plan_id', null)
        ->call('addTask')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    expect($user->tasks()->where('title', 'No plan task')->whereNull('plan_id')->exists())->toBeTrue();
});

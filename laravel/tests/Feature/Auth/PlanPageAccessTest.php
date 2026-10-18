<?php

use App\Models\User;

it('redirects guests from plan page to login', function () {
    $this->get('/plans')->assertRedirect('/login');
});

it('allows authenticated users to visit plan page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/plans')
        ->assertSuccessful();
});

it('renders the plan page with a fixed query count', function () {
    $user = User::factory()->create();
    $user->plans()->create(['name' => 'Work', 'start_date' => '2026-01-01', 'finish_date' => '2026-01-31']);

    $this->actingAs($user);
    $this->expectsDatabaseQueryCount(1);

    $this->get('/plans')->assertSuccessful();
});

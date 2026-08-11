<?php

use App\Models\User;

it('redirects guests from tasks page to login', function () {
    $this->get('/tasks')->assertRedirect('/login');
});

it('allows authenticated users to visit tasks page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/tasks')
        ->assertSuccessful();
});

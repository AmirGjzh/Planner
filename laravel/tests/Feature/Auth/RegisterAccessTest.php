<?php

use App\Models\User;

it('allows guests to visit register page', function () {
    $this->get('/register')->assertSuccessful();
});

it('redirects authenticated users away from register page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/register')
        ->assertRedirect('/dashboard');
});

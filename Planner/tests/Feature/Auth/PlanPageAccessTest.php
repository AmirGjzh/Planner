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

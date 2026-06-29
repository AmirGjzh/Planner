<?php

use App\Models\User;

it('redirects guests from plan page to login', function () {
    $this->get('/plan-page')->assertRedirect('/login');
});

it('allows authenticated users to visit plan page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/plan-page')
        ->assertSuccessful();
});

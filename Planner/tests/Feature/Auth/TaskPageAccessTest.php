<?php

use App\Models\User;

it('redirects guests from task page to login', function () {
    $this->get('/task-page')->assertRedirect('/login');
});

it('allows authenticated users to visit task page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/task-page')
        ->assertSuccessful();
});

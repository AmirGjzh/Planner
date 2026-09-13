<?php

use App\Models\User;

it('redirects guests from the reports page to login', function () {
    $this->get('/reports')->assertRedirect('/login');
});

it('allows authenticated users to visit the reports page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/reports')
        ->assertSuccessful();
});

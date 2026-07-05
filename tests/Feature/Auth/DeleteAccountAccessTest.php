<?php

use App\Models\User;

it('redirects guests away from the profile page', function () {
    $this->get('/profile')->assertRedirect('/login');
});

it('allows authenticated users to visit the profile page', function () {
    $user = User::factory()->create(['country' => 'IR']);

    $this->actingAs($user)
        ->get('/profile')
        ->assertSuccessful()
        ->assertSee('My Profile');
});

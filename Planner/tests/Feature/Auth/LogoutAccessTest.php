<?php

use App\Models\User;

it('logs out an authenticated user and redirects to login', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post('/logout')
        ->assertNoContent();

    $this->assertGuest();
});

it('requires authentication to logout', function () {
    $this->post('/logout')
        ->assertRedirect('/login');
});

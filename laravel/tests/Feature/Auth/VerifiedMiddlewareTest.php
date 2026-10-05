<?php

use App\Models\User;

it('redirects an unverified account to the verification notice from every gated page', function () {
    $user = User::factory()->unverified()->create();

    foreach (['/dashboard', '/profile', '/categories', '/plans', '/tasks', '/reports'] as $path) {
        $this->actingAs($user)
            ->get($path)
            ->assertRedirect('/email/verify');
    }
});

it('keeps logout reachable for an unverified account', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->post('/logout')->assertNoContent();

    $this->assertGuest();
});

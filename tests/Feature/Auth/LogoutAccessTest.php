<?php

use App\Models\User;

it('logs out an authenticated user and flashes a success toast', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post('/logout')
        ->assertNoContent();

    $toast = session('toast');

    expect($toast)->toBeArray()
        ->and($toast['title'])->toBe(__('Logged out successfully'))
        ->and($toast['variant'])->toBe('success');
    $this->assertGuest();
});

it('requires authentication to logout', function () {
    $this->post('/logout')
        ->assertRedirect('/login');
});

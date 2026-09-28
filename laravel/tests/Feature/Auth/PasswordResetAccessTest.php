<?php

use App\Models\User;

it('allows guests to visit the forgot-password page', function () {
    $this->get('/forgot-password')->assertSuccessful();
});

it('redirects authenticated users away from the forgot-password page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/forgot-password')
        ->assertRedirect('/dashboard');
});

it('allows guests to visit the reset-password page', function () {
    $this->get('/reset-password')->assertSuccessful();
});

it('shows the emailed account on the reset-password page', function () {
    $this->get('/reset-password?token=abc123&email=amir@example.com')
        ->assertSuccessful()
        ->assertSee('Resetting password for')
        ->assertSee('amir@example.com')
        ->assertSee('mine-alert-info-bg')
        ->assertDontSee('Link invalid or expired!');
});

it('redirects authenticated users away from the reset-password page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/reset-password')
        ->assertRedirect('/dashboard');
});

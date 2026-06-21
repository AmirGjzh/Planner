<?php

use App\Models\User;

it('allows guests to visit login page', function () {
    $this->get('/login')->assertSuccessful();
});

it('redirects authenticated users away from login page', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect('/dashboard');
});

it('redirects guests from dashboard to login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('allows authenticated users to visit dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSuccessful();
});

<?php

use App\Models\User;

it('redirects guests to the login page', function () {
    $this->get('/email/verify')->assertRedirect('/login');
});

it('shows the notice page to an unverified account', function () {
    $user = User::factory()->unverified()->create(['email' => 'amir@example.com']);

    $this->actingAs($user)
        ->get('/email/verify')
        ->assertSuccessful()
        ->assertSee('<title>'.__('Verify your email address').' — '.config('app.name').'</title>', false)
        ->assertSee('Verify your email address')
        ->assertSee('amir@example.com')
        ->assertSee('Resend verification email');
});

it('redirects a verified account to the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/email/verify')
        ->assertRedirect('/dashboard');
});

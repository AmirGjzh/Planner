<?php

use App\Models\User;

it('renders register for guests without database queries', function () {
    $this->expectsDatabaseQueryCount(0);

    $this->get('/register')->assertSuccessful();
});

it('gives the register page a unique title', function () {
    $this->get('/register')
        ->assertSee('<title>'.__('Create your account').' — '.config('app.name').'</title>', false);
});

it('redirects authenticated users away from register page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/register')
        ->assertRedirect('/dashboard');
});

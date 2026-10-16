<?php

use App\Models\User;

it('redirects guests from category page to login', function () {
    $this->get('/categories')->assertRedirect('/login');
});

it('allows authenticated users to visit category page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/categories')
        ->assertSuccessful();
});

it('renders the category page with a fixed query count', function () {
    $user = User::factory()->create();
    $user->categories()->create(['name' => 'Work']);

    $this->actingAs($user);
    $this->expectsDatabaseQueryCount(2);

    $this->get('/categories')->assertSuccessful();
});

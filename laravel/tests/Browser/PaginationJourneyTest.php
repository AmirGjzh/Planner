<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->user = User::factory()->create([
        'username' => 'amir',
        'email' => 'amir@example.com',
        'password' => 'current-password',
    ]);

    foreach (['Alpha', 'Bravo', 'Charlie', 'Delta', 'Echo', 'Foxtrot', 'Golf'] as $name) {
        $this->user->categories()->create(['name' => $name]);
    }

    RateLimiter::clear('login:127.0.0.1');
});

it('paginates the categories list through the page buttons', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    // Same-second inserts tie on created_at, so the id tiebreaker orders them Alpha…Golf:
    // page 1 carries Alpha…Foxtrot, Golf is the seventh item on page 2.
    $page->click('amir')
        ->click('Categories')
        ->assertPathIs('/categories')
        ->wait(1)
        ->assertSee('Showing 1 to 6 of 7')
        ->assertDontSee('Golf');

    // The pagination bar is live here for the first time — gate both disabled states.
    assertNoAccessibilityIssuesExceptContrast($page);

    $page->click('2')
        ->wait(1)
        ->assertSee('Showing 7 to 7 of 7')
        ->assertSee('Golf');

    assertNoAccessibilityIssuesExceptContrast($page);
});

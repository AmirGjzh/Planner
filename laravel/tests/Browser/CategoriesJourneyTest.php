<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->user = User::factory()->create([
        'username' => 'amir',
        'email' => 'amir@example.com',
        'password' => 'current-password',
    ]);

    RateLimiter::clear('login:127.0.0.1');
    RateLimiter::clear('create-category:'.$this->user->id.'|127.0.0.1');
});

it('has no smoke or accessibility issues on the categories page', function () {
    Category::factory()->create(['user_id' => $this->user->id, 'name' => 'Work']);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Categories')
        ->assertPathIs('/categories')
        ->wait(1)
        ->assertSee('My categories')
        ->assertSee('Work');

    $page->assertNoSmoke();
    assertNoAccessibilityIssuesExceptContrast($page);
});

it('creates a category through the add modal', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Categories')
        ->assertPathIs('/categories')
        ->click('New category')
        ->wait(1)
        ->assertSee('Category name');

    // The open dialog must carry an accessible name (labelled-by contract).
    assertNoAccessibilityIssuesExceptContrast($page);

    $page->fill('add_category', 'Weekend')
        ->click('Create')
        ->wait(1)
        ->assertSee('Weekend')
        ->assertSee('Your category created');

    $this->assertDatabaseHas('categories', [
        'user_id' => $this->user->id,
        'name' => 'Weekend',
    ]);
    $page->assertNoSmoke();
});

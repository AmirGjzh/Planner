<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->user = User::factory()->create([
        'username' => 'amir',
        'email' => 'amir@example.com',
        'password' => 'current-password',
        // Pinned so the trigger's date text can never collide with the
        // fixed years the assertions below look for.
        'birthday' => '1990-06-15',
    ]);

    RateLimiter::clear('login:127.0.0.1');
});

it('matches a Persian-digit query against English year options', function () {
    $persianQuery = strtr('2006', [
        '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
        '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Profile')
        ->assertPathIs('/profile')
        ->click('Edit profile');

    $page->click('[data-datepicker-trigger]')
        ->click('[data-datepicker-select="yearLabel"]')
        // The unfiltered year range is rendered before the query narrows it.
        ->assertSee('2005');

    $page->fill('[x-ref="search"]', $persianQuery)
        ->assertSee('2006')
        ->assertDontSee('2005')
        ->fill('[x-ref="search"]', 'no-such-year')
        ->assertSee('No results found');

    $page->assertNoSmoke();
});

it('matches an English-digit query against Persian category options', function () {
    Category::factory()->create(['user_id' => $this->user->id, 'name' => 'Releases ۱۲۳']);
    Category::factory()->create(['user_id' => $this->user->id, 'name' => 'Archive']);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('Tasks')
        ->assertPathIs('/tasks')
        ->click('New task')
        ->click('Select category')
        ->assertSee('Archive');

    $page->fill('[data-select-search]:visible', '123')
        ->assertSee('Releases ۱۲۳')
        ->assertDontSee('Archive');

    $page->assertNoSmoke();
});

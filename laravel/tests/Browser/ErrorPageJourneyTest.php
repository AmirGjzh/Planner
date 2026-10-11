<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
});

it('renders an accessible themed error page', function () {
    $page = visit('/this-page-does-not-exist');

    $page->wait(1)
        ->assertSee('Page not found');

    assertNoAccessibilityIssuesExceptContrast($page);
});

it('renders a dead link in the signed-in account language', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
        'locale' => 'fa',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    // Real session cookie through the fallback pipeline: the dead link must
    // come back in the account's locale, not the app default.
    $page->navigate('/this-page-does-not-exist')
        ->wait(1)
        ->assertSee('صفحه پیدا نشد');
});

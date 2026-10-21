<?php

use App\Models\User;
use App\Support\Jalali;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
});

it('shows quick links, the GitHub card, and a dynamic copyright for a verified user', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    // Let the page entrance animation settle before the layout-wide assertions.
    $page->wait(1.5)
        ->assertNoSmoke()
        ->assertSeeIn('body > footer', 'Dashboard')
        ->assertSeeIn('body > footer', 'Reports')
        ->assertSeeIn('body > footer', 'Profile')
        ->assertSeeIn('body > footer', 'Log out')
        ->assertSeeIn('body > footer', 'GitHub')
        ->assertSeeIn('body > footer', '© '.config('app.name').' '.date('Y'))
        ->assertDontSeeIn('body > footer', 'Sign in');

    assertNoAccessibilityIssuesExceptContrast($page);
});

it('marks the footer quick link of the current page with aria-current', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('footer a[href$="/tasks"]')
        ->assertPathIs('/tasks')
        ->assertAriaAttribute('footer a[href$="/tasks"]', 'current', 'page');
});

it('keeps the footer off the auth layout', function () {
    $page = visit('/login');

    $page->wait(1)
        ->assertNoSmoke()
        ->assertNotPresent('body > footer');
});

it('translates the footer with the user locale', function () {
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

    $page->wait(1)
        ->assertAriaAttribute('body > footer nav', 'label', 'منوی پایین صفحه')
        ->assertSeeIn('body > footer', 'منو')
        ->assertSeeIn('body > footer', 'تنظیمات')
        ->assertSeeIn('body > footer', '© پلنر '.Jalali::format(now(), 'yyyy').' - تمامی حقوق محفوظ است');
});

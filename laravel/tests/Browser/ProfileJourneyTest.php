<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->user = User::factory()->create([
        'username' => 'amir',
        'email' => 'amir@example.com',
        'password' => 'current-password',
    ]);

    RateLimiter::clear('login:127.0.0.1');
    RateLimiter::clear('change-password:'.$this->user->getKey().'|127.0.0.1');
});

it('signs in and lands on an accessible profile page', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Profile')
        ->assertPathIs('/profile')
        ->assertSee('My profile');

    // Let the card entrance animation settle before the layout-wide assertions.
    $page->wait(1.5)
        ->assertNoSmoke();

    assertNoAccessibilityIssuesExceptContrast($page);
});

it('changes the password through the profile modal and stays signed in', function () {
    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'current-password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    $page->click('amir')
        ->click('Profile')
        ->assertPathIs('/profile')
        ->click('Change password')
        ->fill('current_password', 'wrong-password')
        ->fill('new_password', 'brand-new-password')
        ->fill('new_password_confirmation', 'brand-new-password')
        ->keys('new_password_confirmation', 'Enter')
        ->assertSee('Wrong password!')
        ->fill('current_password', 'current-password')
        ->keys('new_password_confirmation', 'Enter')
        ->assertSee('Password changed')
        ->assertSee('My profile');

    $this->assertAuthenticatedAs($this->user);

    expect(Hash::check('brand-new-password', $this->user->fresh()->password))->toBeTrue();
});

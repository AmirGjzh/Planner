<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('reset-password:127.0.0.1');
});

it('resets the password through the emailed link and returns to sign in', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
    ]);
    $token = Password::createToken($user);

    visit("/reset-password?token={$token}&email={$user->email}")
        ->assertSee('Choose a new password')
        ->assertSee('Resetting password for')
        ->assertSee('amir@example.com')
        ->fill('password', 'brand-new-password')
        ->fill('password_confirmation', 'brand-new-password')
        ->click('Reset password')
        ->assertPathIs('/login')
        ->assertSee('Your password has been reset, sign in to continue');

    expect(Hash::check('brand-new-password', $user->fresh()->password))->toBeTrue();
});

it('keeps the old password when the confirmation does not match', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
    ]);
    $token = Password::createToken($user);

    visit("/reset-password?token={$token}&email={$user->email}")
        ->fill('password', 'brand-new-password')
        ->fill('password_confirmation', 'different-password')
        ->click('Reset password')
        ->assertSee('The passwords don\'t match.')
        ->assertPathIs('/reset-password');

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
    $this->assertGuest();
});

it('rejects a wrong token on submit', function () {
    visit('/reset-password?token=wrong-token&email=amir@example.com')
        ->fill('password', 'brand-new-password')
        ->fill('password_confirmation', 'brand-new-password')
        ->click('Reset password')
        ->assertSee('Link invalid or expired!')
        ->assertDontSee('Reset password');

    $this->assertGuest();
});

it('shows the invalid-link state when the link is missing', function () {
    visit('/reset-password')
        ->assertSee('Link invalid or expired!')
        ->assertDontSee('Reset password');
});

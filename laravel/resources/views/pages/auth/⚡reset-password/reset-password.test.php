<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    RateLimiter::clear('reset-password:127.0.0.1');
});

it('renders successfully with a token', function () {
    Livewire::withQueryParams(['token' => 'abc123', 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->assertStatus(200)
        ->assertSee('Choose a new password')
        ->assertSee('Resetting password for')
        ->assertSee('amir@example.com')
        ->assertDontSee('wire:model="email"', false);
});

it('truncates a long email subtitle', function () {
    $long = str_repeat('a', 250).'@example.com';

    Livewire::withQueryParams(['token' => 'abc123', 'email' => $long])
        ->test('pages::auth.reset-password')
        ->assertSee('mine-text-secondary mb-6 truncate text-center text-sm font-medium', false)
        ->assertSee($long);
});

it('escapes the email in the subtitle', function () {
    Livewire::withQueryParams(['token' => 'abc123', 'email' => '<b>x</b>'])
        ->test('pages::auth.reset-password')
        ->assertSet('reset_error', 'invalid_link')
        ->assertSee('Resetting password for')
        ->assertSee('<b>x</b>')
        ->assertDontSee('<b>', false);
});

it('shows the invalid-link state without a token', function () {
    Livewire::test('pages::auth.reset-password')
        ->assertSet('reset_error', 'invalid_link')
        ->assertDontSee('resetPassword', false);
});

it('shows the invalid-link state when the link carries no email', function () {
    Livewire::withQueryParams(['token' => 'abc123'])
        ->test('pages::auth.reset-password')
        ->assertSet('reset_error', 'invalid_link')
        ->assertDontSee('resetPassword', false);
});

it('validates required fields', function () {
    Livewire::withQueryParams(['token' => 'abc123', 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->call('resetPassword')
        ->assertHasErrors([
            'password' => ['required'],
            'password_confirmation' => ['required'],
        ]);
});

it('dispatches focus-first-error when validation fails', function () {
    Livewire::withQueryParams(['token' => 'abc123', 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->call('resetPassword')
        ->assertHasErrors(['password' => ['required']])
        ->assertDispatched('focus-first-error');
});

it('skips native browser validation on the form', function () {
    Livewire::withQueryParams(['token' => 'abc123', 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->assertSee('novalidate', false);
});

it('validates password length and confirmation', function () {
    Livewire::withQueryParams(['token' => 'abc123', 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->set('password', 'short')
        ->set('password_confirmation', 'different')
        ->call('resetPassword')
        ->assertHasErrors([
            'password' => ['confirmed', 'min'],
        ]);
});

it('rejects an oversized password', function () {
    Livewire::withQueryParams(['token' => 'abc123', 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->set('password', str_repeat('a', 257))
        ->set('password_confirmation', str_repeat('a', 257))
        ->call('resetPassword')
        ->assertHasErrors(['password' => ['max']]);
});

it('treats an invalid email as an invalid link', function () {
    Livewire::withQueryParams(['token' => 'abc123', 'email' => 'bad-email'])
        ->test('pages::auth.reset-password')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('resetPassword')
        ->assertSet('reset_error', 'invalid_link')
        ->assertSee('Link invalid or expired!');
});

it('resets the password and redirects to login with a toast', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
    ]);
    $token = Password::createToken($user);

    Livewire::withQueryParams(['token' => $token, 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('resetPassword')
        ->assertRedirect(route('login'));

    expect(Hash::check('brand-new-password', $user->fresh()->password))->toBeTrue();

    $toast = session('toast');

    expect($toast)->toBeArray()
        ->and($toast['title'])->toBe(__('Your password has been reset, sign in to continue'))
        ->and($toast['variant'])->toBe('success');
});

it('shows an invalid-link error for a wrong token', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
    ]);
    Password::createToken($user);

    Livewire::withQueryParams(['token' => 'wrong-token', 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('resetPassword')
        ->assertSet('reset_error', 'invalid_link')
        ->assertSet('password', '')
        ->assertSee('Link invalid or expired!')
        ->assertDontSee('resetPassword', false);

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('rate limits after too many invalid attempts', function () {
    $user = User::factory()->create(['email' => 'amir@example.com']);
    Password::createToken($user);
    foreach (range(1, 5) as $ignored) {
        Livewire::withQueryParams(['token' => 'wrong-token', 'email' => 'amir@example.com'])
            ->test('pages::auth.reset-password')
            ->set('password', 'brand-new-password')
            ->set('password_confirmation', 'brand-new-password')
            ->call('resetPassword');
    }

    Livewire::withQueryParams(['token' => 'wrong-token', 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('resetPassword')
        ->assertSet('reset_error', 'rate_limited')
        ->assertSee('Too many password-reset attempts!');
});

it('shows the same-password error and keeps the link usable for a retry', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
    ]);
    $token = Password::createToken($user);

    Livewire::withQueryParams(['token' => $token, 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->set('password', 'old-password')
        ->set('password_confirmation', 'old-password')
        ->call('resetPassword')
        ->assertSet('reset_error', 'same_password')
        ->assertSet('password', '')
        ->assertSee('Password didn\'t change!')
        ->assertSee('New password is equal to current one.');

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();

    Livewire::withQueryParams(['token' => $token, 'email' => 'amir@example.com'])
        ->test('pages::auth.reset-password')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('resetPassword')
        ->assertRedirect(route('login'));

    expect(Hash::check('brand-new-password', $user->fresh()->password))->toBeTrue();
});

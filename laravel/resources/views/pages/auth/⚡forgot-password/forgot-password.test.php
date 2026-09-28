<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    RateLimiter::clear('forgot-password:127.0.0.1');
});

it('renders successfully', function () {
    Livewire::test('pages::auth.forgot-password')
        ->assertStatus(200)
        ->assertSee('Reset your password');
});

it('validates required fields', function () {
    Livewire::test('pages::auth.forgot-password')
        ->call('send')
        ->assertHasErrors(['email' => ['required']]);
});

it('validates email format', function () {
    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'bad-email')
        ->call('send')
        ->assertHasErrors(['email' => ['email']]);
});

it('shows the sent state for an existing user', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'amir@example.com']);

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'amir@example.com')
        ->call('send')
        ->assertSet('sent', true)
        ->assertSet('send_error', null);

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('shows the same sent state for an unknown email', function () {
    Notification::fake();

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'ghost@example.com')
        ->call('send')
        ->assertSet('sent', true)
        ->assertSet('send_error', null);

    Notification::assertNothingSent();
});

it('shows the sent state while the broker throttles without a second email', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'amir@example.com']);
    Password::createToken($user);

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'amir@example.com')
        ->call('send')
        ->assertSet('sent', true);

    Notification::assertNothingSent();
});

it('restarts back to the form', function () {
    Notification::fake();
    User::factory()->create(['email' => 'amir@example.com']);

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'amir@example.com')
        ->call('send')
        ->assertSet('sent', true)
        ->call('restart')
        ->assertSet('sent', false)
        ->assertSet('email', '')
        ->assertSet('send_error', null);
});

it('rate limits after too many attempts', function () {
    Notification::fake();
    User::factory()->create(['email' => 'amir@example.com']);
    foreach (range(1, 5) as $ignored) {
        Livewire::test('pages::auth.forgot-password')
            ->set('email', 'amir@example.com')
            ->call('send');
    }

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'amir@example.com')
        ->call('send')
        ->assertSet('send_error', 'rate_limited')
        ->assertSee('Too many reset requests!');
});

it('allows a new request after one minute', function () {
    Notification::fake();
    User::factory()->create(['email' => 'amir@example.com']);
    foreach (range(1, 5) as $ignored) {
        Livewire::test('pages::auth.forgot-password')
            ->set('email', 'amir@example.com')
            ->call('send');
    }
    $this->travel(61)->seconds();

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'amir@example.com')
        ->call('send')
        ->assertSet('send_error', null)
        ->assertSet('sent', true);
});

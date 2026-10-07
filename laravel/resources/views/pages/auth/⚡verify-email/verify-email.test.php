<?php

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

function resendUser(array $attributes = []): User
{
    $user = User::factory()->unverified()->create($attributes);
    RateLimiter::clear('verify-resend:'.$user->getKey().'|127.0.0.1');

    return $user;
}

it('renders the notice with the emailed account and resend button', function () {
    $user = resendUser(['email' => 'amir@example.com']);

    Livewire::actingAs($user)->test('pages::auth.verify-email')
        ->assertStatus(200)
        ->assertSee('Verify your email address')
        ->assertSee('amir@example.com')
        ->assertSee('Resend verification email')
        ->assertSee('Not your account?')
        ->assertSee('Log out');
});

it('wires the resend form with novalidate and a scoped busy target', function () {
    $user = resendUser();

    Livewire::actingAs($user)->test('pages::auth.verify-email')
        ->assertSee('novalidate', false)
        ->assertSee('wire:submit="resend"', false)
        ->assertSee('wire:target="resend"', false);
});

it('truncates a long email subtitle', function () {
    $long = str_repeat('a', 250).'@example.com';
    $user = User::factory()->unverified()->create(['email' => $long]);

    Livewire::actingAs($user)->test('pages::auth.verify-email')
        ->assertSee('mb-6 truncate text-center', false)
        ->assertSee($long);
});

it('escapes the email in the subtitle', function () {
    $user = User::factory()->unverified()->create(['email' => '<b>x</b>']);

    Livewire::actingAs($user)->test('pages::auth.verify-email')
        ->assertSee('Open the verification link in your')
        ->assertSee('<b>x</b>')
        ->assertDontSee('<b>', false);
});

it('renders the notice on the app default theme, not the account theme', function () {
    $defaultBg = config('themes.vars.'.config('themes.default'))['mine-btn-primary-bg'];
    $accountTheme = collect(config('themes.vars'))->keys()->first(
        fn (string $name) => config('themes.vars.'.$name)['mine-btn-primary-bg'] !== $defaultBg
    );

    $this->actingAs(resendUser(['theme' => $accountTheme]))
        ->get('/email/verify')
        ->assertOk()
        ->assertSee('--mine-btn-primary-bg: '.$defaultBg, false)
        ->assertDontSee('--mine-btn-primary-bg: '.config('themes.vars.'.$accountTheme)['mine-btn-primary-bg'], false);
});

it('re-dispatches a flashed session toast on mount', function () {
    session()->flash('toast', [
        'title' => 'You\'re signed in',
        'variant' => 'success',
        'duration' => 6000,
        'position' => 'bottom-center',
    ]);

    Livewire::actingAs(resendUser())->test('pages::auth.verify-email')
        ->assertDispatched('toast',
            title: 'You\'re signed in',
            variant: 'success',
        );
});

it('sends a verification email and dispatches a success toast when resending', function () {
    Notification::fake();
    $user = resendUser();

    Livewire::actingAs($user)->test('pages::auth.verify-email')
        ->call('resend')
        ->assertDispatched('toast',
            title: __('Verification email sent'),
            variant: 'success',
            duration: 3000,
            position: 'bottom-center',
        );

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

it('resends without database queries', function () {
    Notification::fake();
    $user = resendUser();

    $this->expectsDatabaseQueryCount(0);

    Livewire::actingAs($user)->test('pages::auth.verify-email')
        ->call('resend')
        ->assertDispatched('toast');
});

it('shows the rate-limit alert after too many resends', function () {
    $user = resendUser();
    foreach (range(1, 5) as $ignored) {
        RateLimiter::hit('verify-resend:'.$user->getKey().'|127.0.0.1', 60);
    }

    Livewire::actingAs($user)->test('pages::auth.verify-email')
        ->call('resend')
        ->assertSee('Too many verification emails!');
});

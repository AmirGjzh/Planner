<?php

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
});

function verificationLinkFor(User $user, ?Carbon $expiry = null): string
{
    if ($expiry === null) {
        return (new VerifyEmailNotification)->toMail($user)->viewData['url'];
    }

    return rtrim((string) config('app.url'), '/').URL::temporarySignedRoute(
        'verification.verify',
        $expiry,
        [
            'id' => $user->getKey(),
            'hash' => sha1($user->email),
        ],
        false,
    );
}

it('verifies the account and flashes a toast when the signed link is opened', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(verificationLinkFor($user))
        ->assertRedirect('/dashboard');

    $toast = session('toast');

    expect($user->fresh()->email_verified_at)->not->toBeNull()
        ->and($toast['title'])->toBe(__('Email verified'))
        ->and($toast['variant'])->toBe('success');
});

it('verifies with a single timestamp update', function () {
    $user = User::factory()->unverified()->create();
    $link = verificationLinkFor($user);

    $this->expectsDatabaseQueryCount(1);

    $this->actingAs($user)
        ->get($link)
        ->assertRedirect('/dashboard');
});

it('leaves an already verified timestamp in place', function () {
    $user = User::factory()->create(['email_verified_at' => now()->subYear()]);
    $original = $user->fresh()->email_verified_at;

    $this->actingAs($user)
        ->get(verificationLinkFor($user))
        ->assertRedirect('/dashboard');

    expect($user->fresh()->email_verified_at->equalTo($original))->toBeTrue()
        ->and(session('toast'))->toBeNull();
});

it('rejects a link whose signature was tampered with', function () {
    $user = User::factory()->unverified()->create();
    $tampered = preg_replace('/signature=[0-9a-f]+/', 'signature='.str_repeat('0', 64), verificationLinkFor($user));

    $this->actingAs($user)
        ->get($tampered)
        ->assertForbidden();

    expect($user->fresh()->email_verified_at)->toBeNull();
});

it('rejects an expired link', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(verificationLinkFor($user, Carbon::now()->subMinute()))
        ->assertForbidden();

    expect($user->fresh()->email_verified_at)->toBeNull();
});

it('rejects a valid link opened by a different signed-in account', function () {
    $owner = User::factory()->unverified()->create();
    $opener = User::factory()->unverified()->create();

    $this->actingAs($opener)
        ->get(verificationLinkFor($owner))
        ->assertForbidden();

    expect($owner->fresh()->email_verified_at)->toBeNull()
        ->and($opener->fresh()->email_verified_at)->toBeNull();
});

it('redirects a guest opener to the login page', function () {
    $user = User::factory()->unverified()->create();

    $this->get(verificationLinkFor($user))->assertRedirect('/login');
});

it('carries the guest opener through sign-in back to the link and verifies the account', function () {
    $user = User::factory()->unverified()->create(['password' => 'password']);
    $link = verificationLinkFor($user);

    $this->get($link)->assertRedirect('/login');
    expect(session('url.intended'))->toBe($link);

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect($link);

    $this->assertAuthenticatedAs($user);

    $this->get($link)->assertRedirect('/dashboard');

    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

<?php

use App\Actions\Auth\ResetPasswordAction;
use App\Enums\ResetPasswordResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('reset-password:127.0.0.1');
});

function resetPasswordRequest(): Request
{
    $request = Request::create('/reset-password', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());

    return $request;
}

it('resets the password and rotates the remember token', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
        'remember_token' => 'old-remember-token',
    ]);
    $token = Password::createToken($user);

    $result = app(ResetPasswordAction::class)
        ->execute('amir@example.com', $token, 'brand-new-password', resetPasswordRequest());

    $user->refresh();

    expect($result)->toBe(ResetPasswordResult::Reset)
        ->and(Hash::check('brand-new-password', $user->password))->toBeTrue()
        ->and(Hash::check('old-password', $user->password))->toBeFalse()
        ->and($user->remember_token)->not->toBe('old-remember-token')
        ->and(strlen((string) $user->remember_token))->toBe(60)
        ->and($user->password_changed_at)->not->toBeNull()
        ->and(DB::table('password_reset_tokens')->where('email', 'amir@example.com')->exists())->toBeFalse();
});

it('returns invalid link for a wrong token', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
    ]);
    Password::createToken($user);

    $result = app(ResetPasswordAction::class)
        ->execute('amir@example.com', 'wrong-token', 'brand-new-password', resetPasswordRequest());

    expect($result)->toBe(ResetPasswordResult::InvalidLink)
        ->and(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('returns invalid link for an unknown email', function () {
    $user = User::factory()->create(['email' => 'amir@example.com']);
    $token = Password::createToken($user);

    $result = app(ResetPasswordAction::class)
        ->execute('ghost@example.com', $token, 'brand-new-password', resetPasswordRequest());

    expect($result)->toBe(ResetPasswordResult::InvalidLink);
});

it('returns invalid link for an expired token', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
    ]);
    $token = Password::createToken($user);
    DB::table('password_reset_tokens')
        ->where('email', 'amir@example.com')
        ->update(['created_at' => now()->subMinutes(61)]);

    $result = app(ResetPasswordAction::class)
        ->execute('amir@example.com', $token, 'brand-new-password', resetPasswordRequest());

    expect($result)->toBe(ResetPasswordResult::InvalidLink)
        ->and(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('rate limits after too many invalid attempts', function () {
    $user = User::factory()->create(['email' => 'amir@example.com']);
    Password::createToken($user);
    foreach (range(1, 5) as $ignored) {
        app(ResetPasswordAction::class)
            ->execute('amir@example.com', 'wrong-token', 'brand-new-password', resetPasswordRequest());
    }

    $result = app(ResetPasswordAction::class)
        ->execute('amir@example.com', 'wrong-token', 'brand-new-password', resetPasswordRequest());

    expect($result)->toBe(ResetPasswordResult::RateLimited);
});

it('clears the rate-limit bucket after a successful reset', function () {
    $user = User::factory()->create(['email' => 'amir@example.com']);
    $token = Password::createToken($user);

    $result = app(ResetPasswordAction::class)
        ->execute('amir@example.com', $token, 'brand-new-password', resetPasswordRequest());

    expect($result)->toBe(ResetPasswordResult::Reset)
        ->and(RateLimiter::attempts('reset-password:127.0.0.1'))->toBe(0);
});

it('rejects the current password without consuming the token', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
    ]);
    $token = Password::createToken($user);

    $result = app(ResetPasswordAction::class)
        ->execute('amir@example.com', $token, 'old-password', resetPasswordRequest());

    expect($result)->toBe(ResetPasswordResult::SameAsCurrent)
        ->and(Hash::check('old-password', $user->fresh()->password))->toBeTrue()
        ->and(RateLimiter::attempts('reset-password:127.0.0.1'))->toBe(1);

    $retry = app(ResetPasswordAction::class)
        ->execute('amir@example.com', $token, 'brand-new-password', resetPasswordRequest());

    expect($retry)->toBe(ResetPasswordResult::Reset)
        ->and(Hash::check('brand-new-password', $user->fresh()->password))->toBeTrue();
});

it('returns invalid link for the current password without a valid token', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'old-password',
    ]);
    Password::createToken($user);

    $result = app(ResetPasswordAction::class)
        ->execute('amir@example.com', 'wrong-token', 'old-password', resetPasswordRequest());

    expect($result)->toBe(ResetPasswordResult::InvalidLink)
        ->and(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('logs successful and failed resets', function () {
    Log::spy();
    $user = User::factory()->create(['email' => 'amir@example.com']);
    $token = Password::createToken($user);

    app(ResetPasswordAction::class)->execute('amir@example.com', $token, 'password', resetPasswordRequest());
    Log::shouldHaveReceived('info')
        ->with('Password reset rejected, same as current password.', Mockery::on(fn (array $context) => $context['email'] === 'amir@example.com'));

    app(ResetPasswordAction::class)->execute('amir@example.com', $token, 'brand-new-password', resetPasswordRequest());
    Log::shouldHaveReceived('info')
        ->with('Password reset succeeded.', Mockery::on(fn (array $context) => $context['email'] === 'amir@example.com'));

    app(ResetPasswordAction::class)->execute('amir@example.com', 'wrong-token', 'brand-new-password', resetPasswordRequest());
    Log::shouldHaveReceived('warning')
        ->with('Password reset failed, invalid or expired link.', Mockery::on(fn (array $context) => $context['email'] === 'amir@example.com'));

    foreach (range(1, 4) as $ignored) {
        app(ResetPasswordAction::class)->execute('amir@example.com', 'wrong-token', 'brand-new-password', resetPasswordRequest());
    }
    app(ResetPasswordAction::class)->execute('amir@example.com', 'wrong-token', 'brand-new-password', resetPasswordRequest());
    Log::shouldHaveReceived('warning')
        ->with('Password reset rate limited.', Mockery::on(fn (array $context) => isset($context['available_in'])));
});

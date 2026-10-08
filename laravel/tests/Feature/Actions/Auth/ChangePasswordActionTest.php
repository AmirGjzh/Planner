<?php

use App\Actions\Auth\ChangePasswordAction;
use App\Enums\ChangePasswordResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

function changePasswordRequest(): Request
{
    return Request::create('/profile', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

it('changes the password with the correct current password', function () {
    $user = User::factory()->create([
        'password' => 'current-password',
        'remember_token' => 'old-token',
    ]);
    $this->actingAs($user);

    $result = app(ChangePasswordAction::class)
        ->execute($user, 'current-password', 'brand-new-password', changePasswordRequest());

    $fresh = $user->fresh();

    expect($result)->toBe(ChangePasswordResult::Success)
        ->and(Hash::check('brand-new-password', $fresh->password))->toBeTrue()
        ->and(Hash::check('current-password', $fresh->password))->toBeFalse()
        ->and($fresh->password_changed_at)->not->toBeNull()
        ->and($fresh->remember_token)->not->toBe('old-token');
});

it('forbids changing another user password', function () {
    $user = User::factory()->create(['password' => 'current-password']);
    $other = User::factory()->create(['password' => 'other-current']);
    $this->actingAs($user);

    $this->expectException(HttpException::class);

    app(ChangePasswordAction::class)
        ->execute($other, 'other-current', 'brand-new-password', changePasswordRequest());
});

it('re-stamps the changing session auth time so the changer outlives the change', function () {
    $user = User::factory()->create([
        'password' => 'current-password',
    ]);
    $this->actingAs($user);

    session()->put('auth_time', now()->subMinute()->toIso8601String());

    app(ChangePasswordAction::class)
        ->execute($user, 'current-password', 'brand-new-password', changePasswordRequest());

    $fresh = $user->fresh();

    expect(session()->get('auth_time'))->not->toBeNull()
        ->and($fresh->password_changed_at->greaterThan(Carbon::parse(session()->get('auth_time'))))->toBeFalse();
});

it('leaves the password unchanged and counts an attempt for a wrong current password', function () {
    $user = User::factory()->create([
        'password' => 'current-password',
    ]);
    $this->actingAs($user);

    $result = app(ChangePasswordAction::class)
        ->execute($user, 'wrong-password', 'brand-new-password', changePasswordRequest());

    expect($result)->toBe(ChangePasswordResult::WrongCurrentPassword)
        ->and(Hash::check('current-password', $user->fresh()->password))->toBeTrue()
        ->and(RateLimiter::attempts('change-password:'.$user->id.'|127.0.0.1'))->toBe(1);
});

it('returns same as current without updating or moving the bucket', function () {
    $user = User::factory()->create([
        'password' => 'current-password',
    ]);
    $this->actingAs($user);

    $result = app(ChangePasswordAction::class)
        ->execute($user, 'current-password', 'current-password', changePasswordRequest());

    expect($result)->toBe(ChangePasswordResult::SameAsCurrent)
        ->and(Hash::check('current-password', $user->fresh()->password))->toBeTrue()
        ->and(RateLimiter::attempts('change-password:'.$user->id.'|127.0.0.1'))->toBe(0);
});

it('rate limits after repeated wrong current password attempts', function () {
    $user = User::factory()->create([
        'password' => 'current-password',
    ]);
    $this->actingAs($user);

    foreach (range(1, 5) as $ignored) {
        app(ChangePasswordAction::class)
            ->execute($user, 'wrong-password', 'brand-new-password', changePasswordRequest());
    }

    $result = app(ChangePasswordAction::class)
        ->execute($user, 'wrong-password', 'brand-new-password', changePasswordRequest());

    expect($result)->toBe(ChangePasswordResult::RateLimited);
});

it('clears the rate-limit bucket after a successful change', function () {
    $user = User::factory()->create([
        'password' => 'current-password',
    ]);
    $this->actingAs($user);

    app(ChangePasswordAction::class)
        ->execute($user, 'wrong-password', 'brand-new-password', changePasswordRequest());

    app(ChangePasswordAction::class)
        ->execute($user, 'current-password', 'brand-new-password', changePasswordRequest());

    expect(RateLimiter::attempts('change-password:'.$user->id.'|127.0.0.1'))->toBe(0);
});

it('logs wrong-password, limited, same-as-current, and successful change events', function () {
    Log::spy();

    $user = User::factory()->create([
        'password' => 'current-password',
    ]);
    $this->actingAs($user);

    app(ChangePasswordAction::class)
        ->execute($user, 'wrong-password', 'brand-new-password', changePasswordRequest());

    Log::shouldHaveReceived('warning')
        ->with('Password change failed, wrong current password.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));

    foreach (range(1, 5) as $ignored) {
        app(ChangePasswordAction::class)
            ->execute($user, 'wrong-password', 'brand-new-password', changePasswordRequest());
    }

    Log::shouldHaveReceived('warning')
        ->with('Password change rate limited.', Mockery::on(
            fn (array $context) => isset($context['available_in'])
        ));

    RateLimiter::clear('change-password:'.$user->id.'|127.0.0.1');

    app(ChangePasswordAction::class)
        ->execute($user, 'current-password', 'current-password', changePasswordRequest());

    Log::shouldHaveReceived('info')
        ->with('Password change rejected, same as current password.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));

    app(ChangePasswordAction::class)
        ->execute($user, 'current-password', 'brand-new-password', changePasswordRequest());

    Log::shouldHaveReceived('info')
        ->with('Password changed.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));
});

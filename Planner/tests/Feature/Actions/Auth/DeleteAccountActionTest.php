<?php

use App\Actions\Auth\DeleteAccountAction;
use App\Enums\DeleteAccountResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

function deleteAccountRequest(): Request
{
    $request = Request::create('/profile', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());

    return $request;
}

it('returns success and soft-deletes the user with correct password', function () {
    $user = User::factory()->create([
        'password' => 'correct_password',
    ]);
    $this->actingAs($user);

    $result = app(DeleteAccountAction::class)
        ->execute($user, 'correct_password', deleteAccountRequest());

    expect($result)->toBe(DeleteAccountResult::Success)
        ->and(Auth::check())->toBeFalse()
        ->and($user->find($user->id))->toBeNull()
        ->and(User::withTrashed()->find($user->id))->not->toBeNull();
});

it('obfuscates email and username after deletion', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'username' => 'amir_user',
        'password' => 'password',
    ]);
    $this->actingAs($user);

    app(DeleteAccountAction::class)
        ->execute($user, 'password', deleteAccountRequest());

    $deleted = User::withTrashed()->find($user->id);

    expect($deleted->email)->toBe('deleted-user-'.$user->id)
        ->and($deleted->username)->toBe('deleted_user_'.$user->id);
});

it('returns wrong password for incorrect password', function () {
    $user = User::factory()->create([
        'password' => 'correct_password',
    ]);
    $this->actingAs($user);

    $result = app(DeleteAccountAction::class)
        ->execute($user, 'wrong_password', deleteAccountRequest());

    expect($result)->toBe(DeleteAccountResult::WrongPassword)
        ->and(Auth::check())->toBeTrue()
        ->and($user->fresh())->not->toBeNull();
});

it('returns rate limited after repeated wrong password attempts', function () {
    $user = User::factory()->create([
        'password' => 'correct_password',
    ]);
    $this->actingAs($user);

    foreach (range(1, 5) as $ignored) {
        app(DeleteAccountAction::class)
            ->execute($user, 'wrong_password', deleteAccountRequest());
    }

    $result = app(DeleteAccountAction::class)
        ->execute($user, 'wrong_password', deleteAccountRequest());

    expect($result)->toBe(DeleteAccountResult::RateLimited);
});

it('allows deletion again after rate limit expires', function () {
    $user = User::factory()->create([
        'password' => 'correct_password',
    ]);
    $this->actingAs($user);

    foreach (range(1, 5) as $ignored) {
        app(DeleteAccountAction::class)
            ->execute($user, 'wrong_password', deleteAccountRequest());
    }

    $this->travel(61)->seconds();

    $result = app(DeleteAccountAction::class)
        ->execute($user, 'correct_password', deleteAccountRequest());

    expect($result)->toBe(DeleteAccountResult::Success);
});

it('logs deletion events', function () {
    Log::spy();

    $user = User::factory()->create([
        'password' => 'correct_password',
    ]);
    $this->actingAs($user);

    app(DeleteAccountAction::class)
        ->execute($user, 'wrong_password', deleteAccountRequest());

    Log::shouldHaveReceived('warning')
        ->with('Account deletion failed, wrong password.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));

    foreach (range(1, 5) as $ignored) {
        app(DeleteAccountAction::class)
            ->execute($user, 'wrong_password', deleteAccountRequest());
    }

    Log::shouldHaveReceived('warning')
        ->with('Account deletion rate limited.', Mockery::on(
            fn (array $context) => isset($context['seconds_remaining'])
        ));

    RateLimiter::clear('delete-account:'.$user->id.'|127.0.0.1');

    app(DeleteAccountAction::class)
        ->execute($user, 'correct_password', deleteAccountRequest());

    Log::shouldHaveReceived('info')
        ->with('User account deleted.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));
});

<?php

use App\Actions\Profile\UpdateProfileAction;
use App\Enums\UpdateProfileResult;
use App\Enums\UserGender;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

function profileRequest(): Request
{
    return Request::create('/profile', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

function profileRateLimitKey(User $user): string
{
    return 'profile-update:'.$user->id.'|127.0.0.1';
}

it('returns success and updates profile fields', function () {
    $user = User::factory()->create([
        'user_name' => 'old_user',
    ]);
    $this->actingAs($user);

    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, [
        'user_name' => 'new_user',
        'first_name' => ' Amir ',
        'last_name' => ' Planner ',
        'gender' => 'male',
        'country' => 'IR',
        'birth_date' => '2000-01-15',
    ], profileRequest());

    expect($result)->toBe(UpdateProfileResult::Success);

    $user->refresh();

    expect($user->user_name)->toBe('new_user');
    expect($user->first_name)->toBe('Amir');
    expect($user->last_name)->toBe('Planner');
    expect($user->gender)->toBe(UserGender::Male);
    expect($user->country)->toBe('IR');
    expect($user->birth_date->format('Y-m-d'))->toBe('2000-01-15');
});

it('returns username taken when username belongs to another user', function () {
    User::factory()->create([
        'user_name' => 'taken_user',
    ]);

    $user = User::factory()->create([
        'user_name' => 'amir_user',
    ]);
    $this->actingAs($user);

    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, [
        'user_name' => 'taken_user',
    ], profileRequest());

    expect($result)->toBe(UpdateProfileResult::UsernameTaken);
    expect($user->refresh()->user_name)->toBe('amir_user');
});

it('allows keeping the current username', function () {
    $user = User::factory()->create([
        'user_name' => 'amir_user',
    ]);
    $this->actingAs($user);

    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, [
        'user_name' => 'amir_user',
        'first_name' => 'Updated',
    ], profileRequest());

    expect($result)->toBe(UpdateProfileResult::Success);
    expect($user->refresh()->first_name)->toBe('Updated');
});

it('stores blank optional strings as null', function () {
    $user = User::factory()->create([
        'first_name' => 'Amir',
        'last_name' => 'Planner',
    ]);
    $this->actingAs($user);

    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, [
        'user_name' => $user->user_name,
        'first_name' => '   ',
        'last_name' => '',
        'gender' => null,
        'country' => null,
        'birth_date' => null,
    ], profileRequest());

    expect($result)->toBe(UpdateProfileResult::Success);

    $user->refresh();

    expect($user->first_name)->toBeNull();
    expect($user->last_name)->toBeNull();
    expect($user->gender)->toBeNull();
    expect($user->country)->toBeNull();
    expect($user->birth_date)->toBeNull();
});

it('returns rate limited after repeated profile update attempts', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    RateLimiter::clear(profileRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(UpdateProfileAction::class)->execute($user, [
            'user_name' => $user->user_name,
            'first_name' => 'Name '.$attempt,
        ], profileRequest());
    }

    $result = app(UpdateProfileAction::class)->execute($user, [
        'user_name' => $user->user_name,
        'first_name' => 'Blocked',
    ], profileRequest());

    expect($result)->toBe(UpdateProfileResult::RateLimited);
});

it('allows profile updates again after one minute', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    RateLimiter::clear(profileRateLimitKey($user));

    foreach (range(1, 5) as $attempt) {
        app(UpdateProfileAction::class)->execute($user, [
            'user_name' => $user->user_name,
            'first_name' => 'Name '.$attempt,
        ], profileRequest());
    }

    $this->travel(61)->seconds();

    $result = app(UpdateProfileAction::class)->execute($user, [
        'user_name' => $user->user_name,
        'first_name' => 'Allowed',
    ], profileRequest());

    expect($result)->toBe(UpdateProfileResult::Success);
    expect($user->refresh()->first_name)->toBe('Allowed');
});

it('logs failed, limited, and successful profile update events', function () {
    Log::spy();

    User::factory()->create([
        'user_name' => 'taken_user',
    ]);

    $user = User::factory()->create([
        'user_name' => 'amir_user',
    ]);
    $this->actingAs($user);
    RateLimiter::clear(profileRateLimitKey($user));

    app(UpdateProfileAction::class)->execute($user, [
        'user_name' => 'taken_user',
    ], profileRequest());

    Log::shouldHaveReceived('warning')
        ->with('Profile update failed, username already taken.', Mockery::on(
            fn (array $context) => $context['attempted_user_name'] === 'taken_user'
        ));

    foreach (range(1, 5) as $attempt) {
        app(UpdateProfileAction::class)->execute($user, [
            'user_name' => $user->user_name,
            'first_name' => 'Name '.$attempt,
        ], profileRequest());
    }

    Log::shouldHaveReceived('warning')
        ->with('Profile update rate limited.', Mockery::on(
            fn (array $context) => isset($context['seconds_remaining'])
        ));

    RateLimiter::clear(profileRateLimitKey($user));

    app(UpdateProfileAction::class)->execute($user, [
        'user_name' => $user->user_name,
        'first_name' => 'Logged',
    ], profileRequest());

    Log::shouldHaveReceived('info')
        ->with('Profile updated.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));
});

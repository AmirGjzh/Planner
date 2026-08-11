<?php

use App\Actions\Profile\UpdateProfileAction;
use App\Enums\UpdateProfileResult;
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
    return 'update-profile:'.$user->id.'|127.0.0.1';
}

it('returns success and updates profile fields', function () {
    $user = User::factory()->create([
        'username' => 'old_user',
    ]);
    $this->actingAs($user);

    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, 'new_user', ' Amir ', ' Planner ', 'male', 'IR', '2000-01-15', profileRequest());

    expect($result)->toBe(UpdateProfileResult::Success);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'username' => 'new_user',
        'firstname' => 'Amir',
        'lastname' => 'Planner',
        'gender' => 'male',
        'country' => 'IR',
    ]);

    $user->refresh();
    expect($user->birthday->format('Y-m-d'))->toBe('2000-01-15');
});

it('returns username taken when username belongs to another user', function () {
    User::factory()->create([
        'username' => 'taken_user',
    ]);

    $user = User::factory()->create([
        'username' => 'amir_user',
    ]);
    $this->actingAs($user);

    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, 'taken_user', null, null, null, null, null, profileRequest());

    expect($result)->toBe(UpdateProfileResult::UsernameTaken)
        ->and($user->refresh()->username)->toBe('amir_user');
});

it('returns username taken for mixed-case username matching existing lowercase', function () {
    User::factory()->create([
        'username' => 'taken_user',
    ]);

    $user = User::factory()->create([
        'username' => 'amir_user',
    ]);
    $this->actingAs($user);

    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, 'Taken_User', null, null, null, null, null, profileRequest());

    expect($result)->toBe(UpdateProfileResult::UsernameTaken)
        ->and($user->refresh()->username)->toBe('amir_user');
});

it('trims surrounding whitespace and lowercases the username before updating', function () {
    $user = User::factory()->create([
        'username' => 'old_user',
    ]);
    $this->actingAs($user);
    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, '  New_User  ', null, null, null, null, null, profileRequest());

    expect($result)->toBe(UpdateProfileResult::Success);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'username' => 'new_user',
    ]);
});

it('returns username taken for a whitespace-padded mixed-case username matching an existing lowercase one', function () {
    User::factory()->create([
        'username' => 'taken_user',
    ]);

    $user = User::factory()->create([
        'username' => 'amir_user',
    ]);
    $this->actingAs($user);
    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, '  Taken_User  ', null, null, null, null, null, profileRequest());

    expect($result)->toBe(UpdateProfileResult::UsernameTaken)
        ->and($user->refresh()->username)->toBe('amir_user');
});

it('allows keeping the current username', function () {
    $user = User::factory()->create([
        'username' => 'amir_user',
    ]);
    $this->actingAs($user);

    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, 'amir_user', 'Updated', null, null, null, null, profileRequest());

    expect($result)->toBe(UpdateProfileResult::Success)
        ->and($user->refresh()->firstname)->toBe('Updated');
});

it('stores blank optional strings as null', function () {
    $user = User::factory()->create([
        'firstname' => 'Amir',
        'lastname' => 'Planner',
    ]);
    $this->actingAs($user);

    RateLimiter::clear(profileRateLimitKey($user));

    $result = app(UpdateProfileAction::class)->execute($user, $user->username, '   ', '', null, null, null, profileRequest());

    expect($result)->toBe(UpdateProfileResult::Success);

    $user->refresh();

    expect($user->firstname)->toBeNull()
        ->and($user->lastname)->toBeNull()
        ->and($user->gender)->toBeNull()
        ->and($user->country)->toBeNull()
        ->and($user->birthday)->toBeNull();
});

it('returns rate limited after repeated profile update attempts', function () {
    $user = User::factory()->create(['username' => 'amir_user']);
    User::factory()->create(['username' => 'taken_user']);
    $this->actingAs($user);
    RateLimiter::clear(profileRateLimitKey($user));

    foreach (range(1, 5) as $ignored) {
        app(UpdateProfileAction::class)->execute($user, 'taken_user', null, null, null, null, null, profileRequest());
    }

    $result = app(UpdateProfileAction::class)->execute($user, 'another_user', null, null, null, null, null, profileRequest());

    expect($result)->toBe(UpdateProfileResult::RateLimited);
});

it('allows profile updates again after one minute', function () {
    $user = User::factory()->create(['username' => 'amir_user']);
    User::factory()->create(['username' => 'taken_user']);
    $this->actingAs($user);
    RateLimiter::clear(profileRateLimitKey($user));

    foreach (range(1, 5) as $ignored) {
        app(UpdateProfileAction::class)->execute($user, 'taken_user', null, null, null, null, null, profileRequest());
    }

    $this->travel(61)->seconds();

    $result = app(UpdateProfileAction::class)->execute($user, 'amir_user', 'Allowed', null, null, null, null, profileRequest());

    expect($result)->toBe(UpdateProfileResult::Success)
        ->and($user->refresh()->firstname)->toBe('Allowed');
});

it('logs failed, limited, and successful profile update events', function () {
    Log::spy();

    User::factory()->create([
        'username' => 'taken_user',
    ]);

    $user = User::factory()->create([
        'username' => 'amir_user',
    ]);
    $this->actingAs($user);
    RateLimiter::clear(profileRateLimitKey($user));

    app(UpdateProfileAction::class)->execute($user, 'taken_user', null, null, null, null, null, profileRequest());

    Log::shouldHaveReceived('warning')
        ->with('Profile update failed, username already taken.', Mockery::on(
            fn (array $context) => $context['attempted_username'] === 'taken_user'
        ));

    foreach (range(1, 5) as $ignored) {
        app(UpdateProfileAction::class)->execute($user, 'taken_user', null, null, null, null, null, profileRequest());
    }

    Log::shouldHaveReceived('warning')
        ->with('Profile update rate limited.', Mockery::on(
            fn (array $context) => isset($context['seconds_remaining'])
        ));

    RateLimiter::clear(profileRateLimitKey($user));

    app(UpdateProfileAction::class)->execute($user, 'amir_user', 'Logged', null, null, null, null, profileRequest());

    Log::shouldHaveReceived('info')
        ->with('Profile updated.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));
});

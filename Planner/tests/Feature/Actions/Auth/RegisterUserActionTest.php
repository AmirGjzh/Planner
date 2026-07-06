<?php

use App\Actions\Auth\RegisterUserAction;
use App\Enums\RegisterResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('register:127.0.0.1');
});

function registerRequest(): Request
{
    return Request::create('/register', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

it('returns success and creates a user', function () {
    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::Success);

    $user = User::where('email', 'amir@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->user_name)->toBe('amir_user');
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

it('returns username taken when username already exists', function () {
    User::factory()->create([
        'user_name' => 'amir_user',
        'email' => 'old@example.com',
    ]);

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::UsernameTaken);
});

it('returns email taken when email already exists', function () {
    User::factory()->create([
        'user_name' => 'old_user',
        'email' => 'amir@example.com',
    ]);

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::EmailTaken);
});

it('returns rate limited after repeated failed attempts', function () {
    User::factory()->create([
        'user_name' => 'taken_user',
        'email' => 'old@example.com',
    ]);

    foreach (range(1, 5) as $attempt) {
        app(RegisterUserAction::class)
            ->execute('taken_user', 'amir@example.com', 'password123', registerRequest());
    }

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::RateLimited);
});

it('logs failed, limited, and successful register events', function () {
    Log::spy();

    User::factory()->create([
        'user_name' => 'taken_user',
        'email' => 'old@example.com',
    ]);

    app(RegisterUserAction::class)
        ->execute('taken_user', 'amir@example.com', 'password123', registerRequest());

    Log::shouldHaveReceived('warning')
        ->with('Register failed, username already taken.', Mockery::on(
            fn (array $context) => $context['user_name'] === 'taken_user'
        ));

    foreach (range(1, 5) as $attempt) {
        app(RegisterUserAction::class)
            ->execute('taken_user', 'amir@example.com', 'password123', registerRequest());
    }

    Log::shouldHaveReceived('warning')
        ->with('Register rate limited.', Mockery::on(
            fn (array $context) => isset($context['seconds_remaining'])
        ));
    RateLimiter::clear('register:127.0.0.1');

    app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    Log::shouldHaveReceived('info')
        ->with('User Registered.', Mockery::on(
            fn (array $context) => $context['email'] === 'amir@example.com'
        ));
});

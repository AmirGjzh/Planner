<?php

use App\Actions\Auth\RegisterUserAction;
use App\Enums\RegisterResult;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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

    $this->assertDatabaseHas('users', [
        'email' => 'amir@example.com',
        'username' => 'amir_user',
    ]);

    $user = User::where('email', 'amir@example.com')->first();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

it('creates the account with exactly the insert and row reload queries', function () {
    Notification::fake();
    $this->expectsDatabaseQueryCount(2);

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::Success);
});

it('creates the account unverified', function () {
    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::Success);

    $user = User::where('email', 'amir@example.com')->firstOrFail();

    expect($user->email_verified_at)->toBeNull();
});

it('returns username taken when username already exists', function () {
    User::factory()->create([
        'username' => 'amir_user',
        'email' => 'old@example.com',
    ]);

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::UsernameTaken);
});

it('returns email taken when email already exists', function () {
    User::factory()->create([
        'username' => 'old_user',
        'email' => 'amir@example.com',
    ]);

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::EmailTaken);
});

it('reports the username first when both the username and email are taken', function () {
    User::factory()->create([
        'username' => 'amir_user',
        'email' => 'amir@example.com',
    ]);

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::UsernameTaken);
});

it('returns rate limited after repeated failed attempts', function () {
    User::factory()->create([
        'username' => 'taken_user',
        'email' => 'old@example.com',
    ]);

    foreach (range(1, 5) as $ignored) {
        app(RegisterUserAction::class)
            ->execute('taken_user', 'amir@example.com', 'password123', registerRequest());
    }

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::RateLimited);
});

it('counts every attempt and never clears the register bucket', function () {
    foreach (range(1, 5) as $index) {
        $result = app(RegisterUserAction::class)
            ->execute("user{$index}", "user{$index}@example.com", 'password123', registerRequest());

        expect($result)->toBe(RegisterResult::Success);
    }

    expect(RateLimiter::attempts('register:127.0.0.1'))->toBe(5);

    $result = app(RegisterUserAction::class)
        ->execute('another_user', 'another@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::RateLimited)
        ->and(User::where('username', 'another_user')->exists())->toBeFalse();
});

it('trims surrounding whitespace and lowercases username and email before creating', function () {
    $result = app(RegisterUserAction::class)
        ->execute('  Amir_User  ', '  Amir@Example.com  ', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::Success);

    $user = User::where('email', 'amir@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->username)->toBe('amir_user')
        ->and($user->email)->toBe('amir@example.com');
});

it('returns username taken for a whitespace-padded mixed-case username matching an existing lowercase one', function () {
    User::factory()->create([
        'username' => 'amir_user',
        'email' => 'old@example.com',
    ]);

    $result = app(RegisterUserAction::class)
        ->execute('  Amir_User  ', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::UsernameTaken);
});

it('sends the verification notification after a successful signup', function () {
    Notification::fake();

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    $user = User::where('email', 'amir@example.com')->firstOrFail();

    expect($result)->toBe(RegisterResult::Success);
    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

it('creates the account and still succeeds when the verification dispatch fails', function () {
    Log::spy();
    config(['queue.default' => 'missing-queue']);

    $result = app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    expect($result)->toBe(RegisterResult::Success);
    $this->assertDatabaseHas('users', ['email' => 'amir@example.com']);
    Log::shouldHaveReceived('error')
        ->with('Verification email dispatch failed.', Mockery::on(
            fn (array $context) => $context['email'] === 'amir@example.com'
                && isset($context['exception'], $context['message'])
        ));
});

it('logs failed, limited, and successful register events', function () {
    Log::spy();

    User::factory()->create([
        'username' => 'taken_user',
        'email' => 'old@example.com',
    ]);

    app(RegisterUserAction::class)
        ->execute('taken_user', 'amir@example.com', 'password123', registerRequest());

    Log::shouldHaveReceived('warning')
        ->with('Register failed, username already taken.', Mockery::on(
            fn (array $context) => $context['username'] === 'taken_user'
        ));

    foreach (range(1, 5) as $ignored) {
        app(RegisterUserAction::class)
            ->execute('taken_user', 'amir@example.com', 'password123', registerRequest());
    }

    Log::shouldHaveReceived('warning')
        ->with('Register rate limited.', Mockery::on(
            fn (array $context) => isset($context['available_in'])
        ));
    RateLimiter::clear('register:127.0.0.1');

    app(RegisterUserAction::class)
        ->execute('amir_user', 'amir@example.com', 'password123', registerRequest());

    Log::shouldHaveReceived('info')
        ->with('User registered.', Mockery::on(
            fn (array $context) => $context['email'] === 'amir@example.com'
        ));
});

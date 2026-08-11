<?php

use App\Actions\Auth\LoginUserAction;
use App\Enums\LoginResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login:amir@example.com|127.0.0.1');
});

function loginRequest(): Request
{
    $request = Request::create('/login', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());

    return $request;
}

it('returns success for valid credentials', function () {
    User::factory()->create(['email' => 'amir@example.com', 'password' => 'password']);
    $result = app(LoginUserAction::class)
        ->execute('amir@example.com', 'password', true, loginRequest());
    expect($result)->toBe(LoginResult::Success);
    $this->assertAuthenticated();
});

it('returns fail for invalid credentials', function () {
    User::factory()->create(['email' => 'amir@example.com', 'password' => 'password']);
    $result = app(LoginUserAction::class)
        ->execute('amir@example.com', 'wrong-password', true, loginRequest());
    expect($result)->toBe(LoginResult::Fail);
    $this->assertGuest();
});

it('returns rate limited after repeated failures', function () {
    User::factory()->create(['email' => 'amir@example.com', 'password' => 'password']);
    foreach (range(1, 5) as $ignored) {
        app(LoginUserAction::class)->execute('amir@example.com', 'wrong-password', true, loginRequest());
    }
    $result = app(LoginUserAction::class)
        ->execute('amir@example.com', 'password', true, loginRequest());
    expect($result)->toBe(LoginResult::RateLimited);
});

it('normalizes email casing before authenticating', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    $result = app(LoginUserAction::class)
        ->execute('Amir@Example.com', 'password', true, loginRequest());
    expect($result)->toBe(LoginResult::Success);
    $this->assertAuthenticated();
});

it('logs failed, limited, and successful login events', function () {
    Log::spy();
    User::factory()->create(['email' => 'amir@example.com', 'password' => 'password']);
    app(LoginUserAction::class)->execute('amir@example.com', 'wrong-password', true, loginRequest());
    Log::shouldHaveReceived('warning')
        ->with('Login failed.', Mockery::on(fn (array $context) => $context['email'] === 'amir@example.com'));
    foreach (range(1, 5) as $ignored) {
        app(LoginUserAction::class)->execute('amir@example.com', 'wrong-password', true, loginRequest());
    }
    Log::shouldHaveReceived('warning')
        ->with('Login rate limited.', Mockery::on(fn (array $context) => isset($context['available_in'])));
    RateLimiter::clear('login:amir@example.com|127.0.0.1');
    app(LoginUserAction::class)->execute('amir@example.com', 'password', true, loginRequest());
    Log::shouldHaveReceived('info')
        ->with('User logged in.', Mockery::on(fn (array $context) => isset($context['user_id'])));
});

it('trims surrounding whitespace and lowercases the email before authenticating', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    $result = app(LoginUserAction::class)
        ->execute('  AmIr@Example.com  ', 'password', true, loginRequest());
    expect($result)->toBe(LoginResult::Success);
    $this->assertAuthenticated();
});

it('blocks login for a soft-deleted user', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    $user->delete();

    $result = app(LoginUserAction::class)
        ->execute('amir@example.com', 'password', true, loginRequest());
    expect($result)->toBe(LoginResult::Fail);
    $this->assertGuest();
});

it('rate limits attempts on a soft-deleted account too', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    $user->delete();

    foreach (range(1, 5) as $ignored) {
        app(LoginUserAction::class)->execute('amir@example.com', 'password', true, loginRequest());
    }
    $result = app(LoginUserAction::class)
        ->execute('amir@example.com', 'password', true, loginRequest());
    expect($result)->toBe(LoginResult::RateLimited);
});

<?php

use App\Actions\Auth\LoginUserAction;
use App\Enums\LoginResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
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
    expect($result)->toBe(LoginResult::Fail)
        ->and(RateLimiter::attempts('login:127.0.0.1'))->toBe(1);
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

it('clears the rate-limit bucket after a successful login', function () {
    User::factory()->create(['email' => 'amir@example.com', 'password' => 'password']);

    app(LoginUserAction::class)
        ->execute('amir@example.com', 'wrong-password', true, loginRequest());

    expect(RateLimiter::attempts('login:127.0.0.1'))->toBe(1);

    $result = app(LoginUserAction::class)
        ->execute('amir@example.com', 'password', true, loginRequest());

    expect($result)->toBe(LoginResult::Success)
        ->and(RateLimiter::attempts('login:127.0.0.1'))->toBe(0);
});

it('rotates the session id and CSRF token on success but not on failure', function () {
    User::factory()->create(['email' => 'amir@example.com', 'password' => 'password']);
    session()->start();
    $sessionId = session()->getId();
    $csrfToken = session()->token();

    app(LoginUserAction::class)
        ->execute('amir@example.com', 'wrong-password', false, loginRequest());

    expect(session()->getId())->toBe($sessionId)
        ->and(session()->token())->toBe($csrfToken);

    app(LoginUserAction::class)
        ->execute('amir@example.com', 'password', false, loginRequest());

    expect(session()->getId())->not->toBe($sessionId)
        ->and(session()->token())->not->toBe($csrfToken);
});

it('queues the recaller cookie when remember is requested', function () {
    $user = User::factory()->create(['email' => 'amir@example.com', 'password' => 'password']);

    app(LoginUserAction::class)
        ->execute('amir@example.com', 'password', true, loginRequest());

    $recaller = collect(Cookie::getQueuedCookies())
        ->first(fn ($cookie) => $cookie->getName() === Auth::getRecallerName());

    expect($recaller)->not->toBeNull()
        ->and($recaller->getValue())->toContain((string) $user->getAuthIdentifier());
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
    RateLimiter::clear('login:127.0.0.1');
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

it('queues the session hint cookie on success', function () {
    User::factory()->create(['email' => 'amir@example.com', 'password' => 'password']);

    app(LoginUserAction::class)
        ->execute('amir@example.com', 'password', false, loginRequest());

    $queued = collect(Cookie::getQueuedCookies())
        ->first(fn ($cookie) => $cookie->getName() === 'session_hint');

    expect($queued)->not->toBeNull()
        ->and($queued->getValue())->toBe('1')
        ->and($queued->getExpiresTime())->toBeGreaterThan(now()->addDays(29)->getTimestamp())
        ->and($queued->getExpiresTime())->toBeLessThan(now()->addDays(31)->getTimestamp());
});

it('does not queue the session hint cookie on failure', function () {
    User::factory()->create(['email' => 'amir@example.com', 'password' => 'password']);

    app(LoginUserAction::class)
        ->execute('amir@example.com', 'wrong-password', false, loginRequest());

    $queued = collect(Cookie::getQueuedCookies())
        ->first(fn ($cookie) => $cookie->getName() === 'session_hint');

    expect($queued)->toBeNull();
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

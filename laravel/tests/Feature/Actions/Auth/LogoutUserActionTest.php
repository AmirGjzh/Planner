<?php

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\LogoutUserAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

it('returns success and logs out the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    expect(Auth::check())->toBeTrue();

    $request = Request::create('/logout', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());

    app(LogoutUserAction::class)->execute($request);

    expect(Auth::check())->toBeFalse();
});

it('invalidates the session after logout', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $request = Request::create('/logout', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());
    $sessionId = $request->session()->getId();

    app(LogoutUserAction::class)->execute($request);

    expect($request->session()->getId())->not->toBe($sessionId);
});

it('regenerates the CSRF token after logout', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $request = Request::create('/logout', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());
    $oldToken = $request->session()->token();

    app(LogoutUserAction::class)->execute($request);

    expect($request->session()->token())->not->toBe($oldToken);
});

it('logs the logout event', function () {
    Log::spy();

    $user = User::factory()->create();
    $this->actingAs($user);

    $request = Request::create('/logout', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());

    app(LogoutUserAction::class)->execute($request);

    Log::shouldHaveReceived('info')
        ->with('User logged out.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->id
        ));
});

it('rotates the remember token so remember-me sessions die with logout', function () {
    $user = User::factory()->create([
        'remember_token' => 'old-token',
    ]);
    $this->actingAs($user);

    $request = Request::create('/logout', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());

    app(LogoutUserAction::class)->execute($request);

    expect($user->fresh()->remember_token)->not->toBe('old-token');
});

it('forgets the session hint cookie on logout', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $request = Request::create('/logout', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());

    app(LogoutUserAction::class)->execute($request);

    $queued = collect(Cookie::getQueuedCookies())
        ->first(fn ($cookie) => $cookie->getName() === LoginUserAction::SESSION_HINT);

    expect($queued)->not->toBeNull()
        ->and($queued->getValue())->toBeNull()
        ->and($queued->getExpiresTime())->toBeLessThan(time());
});

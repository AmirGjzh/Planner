<?php

use App\Actions\Auth\ForgotPasswordAction;
use App\Enums\SendResetLinkResult;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('forgot-password:127.0.0.1');
});

function forgotPasswordRequest(): Request
{
    $request = Request::create('/forgot-password', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $request->setLaravelSession(app('session')->driver());

    return $request;
}

it('sends a reset notification and returns sent for an existing user', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'amir@example.com']);

    $result = app(ForgotPasswordAction::class)
        ->execute('amir@example.com', forgotPasswordRequest());

    expect($result)->toBe(SendResetLinkResult::Sent);
    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('returns sent without a notification for an unknown email', function () {
    Notification::fake();

    $result = app(ForgotPasswordAction::class)
        ->execute('ghost@example.com', forgotPasswordRequest());

    expect($result)->toBe(SendResetLinkResult::Sent);
    Notification::assertNothingSent();
});

it('links to the reset-password page in the notification', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'amir@example.com']);

    app(ForgotPasswordAction::class)->execute('amir@example.com', forgotPasswordRequest());

    Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification) use ($user) {
        $mail = $notification->toMail($user);
        $parts = parse_url($mail->actionUrl);
        parse_str($parts['query'] ?? '', $query);

        return ($parts['path'] ?? null) === '/reset-password'
            && ($query['token'] ?? null) === $notification->token
            && ($query['email'] ?? null) === 'amir@example.com'
            && $mail->view === 'mail.reset-password'
            && $mail->subject === 'Reset your password';
    });
});

it('returns sent without a second notification while the broker throttles', function () {
    Log::spy();
    Notification::fake();
    $user = User::factory()->create(['email' => 'amir@example.com']);

    app(ForgotPasswordAction::class)->execute('amir@example.com', forgotPasswordRequest());
    $result = app(ForgotPasswordAction::class)->execute('amir@example.com', forgotPasswordRequest());

    expect($result)->toBe(SendResetLinkResult::Sent);
    Notification::assertSentToTimes($user, ResetPasswordNotification::class, 1);
    Log::shouldHaveReceived('info')
        ->with('Password reset link throttled, a token was recently created.', Mockery::on(fn (array $context) => $context['email'] === 'amir@example.com'));
});

it('hits the bucket for unknown emails and keeps it across successes', function () {
    Notification::fake();
    User::factory()->create(['email' => 'amir@example.com']);

    app(ForgotPasswordAction::class)->execute('ghost@example.com', forgotPasswordRequest());
    expect(RateLimiter::attempts('forgot-password:127.0.0.1'))->toBe(1);

    app(ForgotPasswordAction::class)->execute('amir@example.com', forgotPasswordRequest());
    expect(RateLimiter::attempts('forgot-password:127.0.0.1'))->toBe(2);
});

it('rate limits after too many requests even for an existing email', function () {
    Notification::fake();
    User::factory()->create(['email' => 'amir@example.com']);
    foreach (range(1, 5) as $ignored) {
        app(ForgotPasswordAction::class)->execute('amir@example.com', forgotPasswordRequest());
    }

    $result = app(ForgotPasswordAction::class)
        ->execute('amir@example.com', forgotPasswordRequest());

    expect($result)->toBe(SendResetLinkResult::RateLimited);
});

it('allows a new request after one minute', function () {
    Notification::fake();
    User::factory()->create(['email' => 'amir@example.com']);
    foreach (range(1, 5) as $ignored) {
        app(ForgotPasswordAction::class)->execute('amir@example.com', forgotPasswordRequest());
    }
    $this->travel(61)->seconds();

    $result = app(ForgotPasswordAction::class)
        ->execute('amir@example.com', forgotPasswordRequest());

    expect($result)->toBe(SendResetLinkResult::Sent);
});

it('returns sent and logs an error when the mail transport fails', function () {
    Log::spy();
    config(['mail.default' => 'missing-transport']);
    User::factory()->create(['email' => 'amir@example.com']);

    $result = app(ForgotPasswordAction::class)
        ->execute('amir@example.com', forgotPasswordRequest());

    expect($result)->toBe(SendResetLinkResult::Sent);
    Log::shouldHaveReceived('error')
        ->with('Password reset link could not be sent.', Mockery::on(fn (array $context) => $context['email'] === 'amir@example.com' && isset($context['exception'], $context['message'])));
});

it('returns sent and logs an error when the queue rejects the notification', function () {
    Log::spy();
    config(['queue.default' => 'missing-queue']);
    User::factory()->create(['email' => 'amir@example.com']);

    $result = app(ForgotPasswordAction::class)
        ->execute('amir@example.com', forgotPasswordRequest());

    expect($result)->toBe(SendResetLinkResult::Sent);
    Log::shouldHaveReceived('error')
        ->with('Password reset link could not be sent.', Mockery::on(fn (array $context) => $context['email'] === 'amir@example.com'));
});

it('logs sent, unknown, and rate-limited outcomes', function () {
    Log::spy();
    Notification::fake();
    User::factory()->create(['email' => 'amir@example.com']);

    app(ForgotPasswordAction::class)->execute('amir@example.com', forgotPasswordRequest());
    Log::shouldHaveReceived('info')
        ->with('Password reset link sent.', Mockery::on(fn (array $context) => $context['email'] === 'amir@example.com'));

    app(ForgotPasswordAction::class)->execute('ghost@example.com', forgotPasswordRequest());
    Log::shouldHaveReceived('warning')
        ->with('Password reset link requested for an unknown email.', Mockery::on(fn (array $context) => $context['email'] === 'ghost@example.com'));

    foreach (range(1, 3) as $ignored) {
        app(ForgotPasswordAction::class)->execute('ghost@example.com', forgotPasswordRequest());
    }
    app(ForgotPasswordAction::class)->execute('ghost@example.com', forgotPasswordRequest());
    Log::shouldHaveReceived('warning')
        ->with('Password reset link rate limited.', Mockery::on(fn (array $context) => isset($context['available_in'])));
});

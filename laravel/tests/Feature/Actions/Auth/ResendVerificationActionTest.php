<?php

use App\Actions\Auth\ResendVerificationAction;
use App\Enums\ResendVerificationResult;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

function unverifiedActionUser(): User
{
    $user = User::factory()->unverified()->create();
    RateLimiter::clear('verify-resend:'.$user->getKey().'|127.0.0.1');

    return $user;
}

function resendVerificationRequest(): Request
{
    return Request::create('/email/verify', 'GET', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

it('sends the verification notification, logs the send, and returns sent', function () {
    Log::spy();
    Notification::fake();
    $user = unverifiedActionUser();

    $result = app(ResendVerificationAction::class)
        ->execute($user, resendVerificationRequest());

    expect($result)->toBe(ResendVerificationResult::Sent);
    Notification::assertSentTo($user, VerifyEmailNotification::class);
    Log::shouldHaveReceived('info')
        ->with('Verification email sent.', Mockery::on(
            fn (array $context) => $context['email'] === $user->email
        ));
});

it('counts every resend and keeps the bucket across successful sends', function () {
    Notification::fake();
    $user = unverifiedActionUser();

    app(ResendVerificationAction::class)->execute($user, resendVerificationRequest());
    expect(RateLimiter::attempts('verify-resend:'.$user->getKey().'|127.0.0.1'))->toBe(1);

    app(ResendVerificationAction::class)->execute($user, resendVerificationRequest());
    expect(RateLimiter::attempts('verify-resend:'.$user->getKey().'|127.0.0.1'))->toBe(2);
});

it('rate limits after five sends and logs the wait', function () {
    Log::spy();
    Notification::fake();
    $user = unverifiedActionUser();

    foreach (range(1, 5) as $ignored) {
        app(ResendVerificationAction::class)->execute($user, resendVerificationRequest());
    }

    $result = app(ResendVerificationAction::class)->execute($user, resendVerificationRequest());

    expect($result)->toBe(ResendVerificationResult::RateLimited);
    Log::shouldHaveReceived('warning')
        ->with('Resend verification rate limited.', Mockery::on(
            fn (array $context) => isset($context['available_in'])
        ));
});

it('returns already verified without sending for a verified account', function () {
    Notification::fake();
    $user = User::factory()->create();

    $result = app(ResendVerificationAction::class)->execute($user, resendVerificationRequest());

    expect($result)->toBe(ResendVerificationResult::AlreadyVerified);
    Notification::assertNothingSent();
});

it('returns sent and logs an error when the queue rejects the notification', function () {
    Log::spy();
    config(['queue.default' => 'missing-queue']);
    $user = unverifiedActionUser();

    $result = app(ResendVerificationAction::class)->execute($user, resendVerificationRequest());

    expect($result)->toBe(ResendVerificationResult::Sent);
    Log::shouldHaveReceived('error')
        ->with('Verification email could not be sent.', Mockery::on(
            fn (array $context) => $context['email'] === $user->email
                && isset($context['exception'], $context['message'])
        ));
});

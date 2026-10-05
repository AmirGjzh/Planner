<?php

use App\Actions\Auth\VerifyEmailAction;
use App\Enums\VerifyEmailResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

function verifyActionRequest(): Request
{
    return Request::create('/email/verify/1/abc', 'GET', server: ['REMOTE_ADDR' => '127.0.0.1']);
}

it('marks the email as verified and logs the outcome', function () {
    Log::spy();
    $user = User::factory()->unverified()->create();

    $result = app(VerifyEmailAction::class)->execute($user, verifyActionRequest());

    expect($result)->toBe(VerifyEmailResult::Verified)
        ->and($user->fresh()->email_verified_at)->not->toBeNull();
    Log::shouldHaveReceived('info')
        ->with('User email verified.', Mockery::on(
            fn (array $context) => $context['user_id'] === $user->getKey()
                && isset($context['ip'])
        ));
});

it('returns already verified without moving the timestamp', function () {
    $user = User::factory()->create(['email_verified_at' => now()->subYear()]);
    $original = $user->fresh()->email_verified_at;

    $result = app(VerifyEmailAction::class)->execute($user, verifyActionRequest());

    expect($result)->toBe(VerifyEmailResult::AlreadyVerified)
        ->and($user->fresh()->email_verified_at->equalTo($original))->toBeTrue();
});

<?php

namespace App\Actions\Auth;

use App\Enums\ResendVerificationResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Throwable;

final class ResendVerificationAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(User $user, Request $request): ResendVerificationResult
    {
        if ($user->hasVerifiedEmail()) {
            return ResendVerificationResult::AlreadyVerified;
        }

        $rate_limit_key = $this->rateLimitKey($user, $request);
        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Resend verification rate limited.', [
                'email' => $user->email,
                'ip' => $request->ip(),
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return ResendVerificationResult::RateLimited;
        }

        RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            $this->logger->error('Verification email could not be sent.', [
                'email' => $user->email,
                'ip' => $request->ip(),
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return ResendVerificationResult::Sent;
        }

        $this->logger->info('Verification email sent.', [
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return ResendVerificationResult::Sent;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('verify-resend:'.$user->getKey().'|'.$request->ip());
    }
}

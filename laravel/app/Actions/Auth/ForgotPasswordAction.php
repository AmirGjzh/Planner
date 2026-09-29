<?php

namespace App\Actions\Auth;

use App\Enums\SendResetLinkResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Throwable;

final class ForgotPasswordAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(string $email, Request $request): SendResetLinkResult
    {
        $email = Str::lower(Str::trim($email));

        $rate_limit_key = $this->rateLimitKey($request);
        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Password reset link rate limited.', [
                'email' => $email,
                'ip' => $request->ip(),
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return SendResetLinkResult::RateLimited;
        }

        RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);

        try {
            $status = Password::sendResetLink(['email' => $email]);
        } catch (Throwable $e) {
            $this->logger->error('Password reset link could not be sent.', [
                'email' => $email,
                'ip' => $request->ip(),
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return SendResetLinkResult::Sent;
        }

        if ($status === Password::RESET_LINK_SENT) {
            $this->logger->info('Password reset link sent.', [
                'email' => $email,
                'ip' => $request->ip(),
            ]);
        } elseif ($status === Password::RESET_THROTTLED) {
            $this->logger->info('Password reset link throttled, a token was recently created.', [
                'email' => $email,
                'ip' => $request->ip(),
            ]);
        } else {
            $this->logger->warning('Password reset link requested for an unknown email.', [
                'email' => $email,
                'ip' => $request->ip(),
            ]);
        }

        return SendResetLinkResult::Sent;
    }

    private function rateLimitKey(Request $request): string
    {
        return Str::transliterate('forgot-password:'.$request->ip());
    }
}

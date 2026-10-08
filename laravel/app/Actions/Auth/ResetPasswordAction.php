<?php

namespace App\Actions\Auth;

use App\Enums\ResetPasswordResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class ResetPasswordAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(
        string $email,
        string $token,
        string $password,
        Request $request,
    ): ResetPasswordResult {
        $email = Str::lower(Str::trim($email));

        $rate_limit_key = $this->rateLimitKey($request);
        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Password reset rate limited.', [
                'email' => $email,
                'ip' => $request->ip(),
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return ResetPasswordResult::RateLimited;
        }

        // Every accepted request counts — invalid links included — and only a
        // completed reset clears the bucket (contracts: .ai/rules/auth.md).
        RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);

        $user = Password::getUser(['email' => $email]);
        // Order matters: Hash::check sits behind a valid token on purpose — testing
        // a candidate password against the stored hash without one would be an
        // oracle for guessing the current password.
        if ($user !== null && Password::tokenExists($user, $token) && Hash::check($password, $user->password)) {
            $this->logger->info('Password reset rejected, same as current password.', [
                'email' => $email,
                'ip' => $request->ip(),
            ]);

            return ResetPasswordResult::SameAsCurrent;
        }

        $status = Password::reset(
            ['email' => $email, 'token' => $token, 'password' => $password],
            function (User $user, string $password) {
                // Rotating the remember token kills any remember-me cookie issued
                // before the reset — a hijacked device must not stay logged in.
                $user->forceFill([
                    'password' => $password,
                    'password_changed_at' => now(),
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->logger->warning('Password reset failed, invalid or expired link.', [
                'email' => $email,
                'ip' => $request->ip(),
            ]);

            return ResetPasswordResult::InvalidLink;
        }

        RateLimiter::clear($rate_limit_key);
        $this->logger->info('Password reset succeeded.', [
            'email' => $email,
            'ip' => $request->ip(),
        ]);

        return ResetPasswordResult::Reset;
    }

    private function rateLimitKey(Request $request): string
    {
        return Str::transliterate('reset-password:'.$request->ip());
    }
}

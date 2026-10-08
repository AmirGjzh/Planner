<?php

namespace App\Actions\Auth;

use App\Enums\ChangePasswordResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class ChangePasswordAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    /**
     * Verify the current password, then store the new one with a
     * `password_changed_at` stamp and remember-token rotation (parity with
     * `ResetPasswordAction`). The changing session's `auth_time` is re-stamped
     * right after the save so `ExpireSessionsOnPasswordChange` evicts every
     * older session but never the one that made the change.
     */
    public function execute(
        User $user,
        string $current_password,
        string $new_password,
        Request $request,
    ): ChangePasswordResult {
        abort_unless($user->is(auth()->user()), 403);

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Password change rate limited.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return ChangePasswordResult::RateLimited;
        }

        if (! Hash::check($current_password, $user->password)) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Password change failed, wrong current password.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return ChangePasswordResult::WrongCurrentPassword;
        }

        if (Hash::check($new_password, $user->password)) {
            $this->logger->info('Password change rejected, same as current password.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return ChangePasswordResult::SameAsCurrent;
        }

        $user->forceFill([
            'password' => $new_password,
            'password_changed_at' => now(),
            'remember_token' => Str::random(60),
        ])->save();

        session()->put('auth_time', now()->toIso8601String());

        RateLimiter::clear($rate_limit_key);
        $this->logger->info('Password changed.', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return ChangePasswordResult::Success;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('change-password:'.$user->id.'|'.$request->ip());
    }
}

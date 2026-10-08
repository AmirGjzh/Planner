<?php

namespace App\Actions\Auth;

use App\Enums\LoginResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class LoginUserAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * One-shot hint that the visitor's session died: a 30-day cookie that
     * outlives the session. Expiry cannot be detected from inside a session,
     * so the login page reads this on arrival, flashes "session expired",
     * and forgets it (contracts: .ai/rules/auth.md).
     */
    public const string SESSION_HINT = 'session_hint';

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    private const int SESSION_HINT_MINUTES = 30 * 24 * 60;

    public function execute(
        string $email,
        string $password,
        bool $remember,
        Request $request,
    ): LoginResult {
        $email = Str::lower(Str::trim($email));

        $rate_limit_key = $this->rateLimitKey($request);
        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Login rate limited.', [
                'email' => $email,
                'ip' => $request->ip(),
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return LoginResult::RateLimited;
        }
        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Login failed.', [
                'email' => $email,
                'ip' => $request->ip(),
            ]);

            return LoginResult::Fail;
        }
        // Clear-on-success: this bucket counts failed credential guesses, so one
        // correct login empties it (register never clears — see RegisterUserAction).
        RateLimiter::clear($rate_limit_key);
        session()->regenerate();
        session()->regenerateToken();
        Cookie::queue(self::SESSION_HINT, '1', self::SESSION_HINT_MINUTES);
        $this->logger->info('User logged in.', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
        ]);

        return LoginResult::Success;
    }

    private function rateLimitKey(Request $request): string
    {
        return Str::transliterate('login:'.$request->ip());
    }
}

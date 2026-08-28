<?php

namespace App\Actions\Auth;

use App\Enums\LoginResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class LoginUserAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

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
        RateLimiter::clear($rate_limit_key);
        session()->regenerate();
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

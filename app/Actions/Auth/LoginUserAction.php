<?php

namespace App\Actions\Auth;

use App\Enums\LoginResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

final class LoginUserAction
{
    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(
        string $email,
        string $password,
        bool $remember,
        Request $request,
    ): LoginResult {
        $rateLimitKey = $this->rateLimitKey($email, $request);
        if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            Log::warning('Login rate limited.', [
                'email' => Str::lower($email),
                'ip' => $request->ip(),
                'seconds_remaining' => $seconds,
            ]);

            return LoginResult::RateLimited;
        }
        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            RateLimiter::hit($rateLimitKey, self::DECAY_SECONDS);
            Log::warning('Login failed.', [
                'email' => Str::lower($email),
                'ip' => $request->ip(),
            ]);

            return LoginResult::Fail;
        }
        RateLimiter::clear($rateLimitKey);
        session()->regenerate();
        Log::info('User logged in.', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
        ]);

        return LoginResult::Success;
    }

    private function rateLimitKey(string $email, Request $request): string
    {
        return Str::transliterate('login:'.Str::lower($email).'|'.$request->ip());
    }
}

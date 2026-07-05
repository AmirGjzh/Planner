<?php

namespace App\Actions\Auth;

use App\Enums\RegisterResult;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class RegisterUserAction
{
    public function execute(
        string $user_name,
        string $email,
        string $password,
        Request $request,
    ): RegisterResult {
        $rateLimitKey = $this->rateLimitKey($email, $request);
        $email = Str::lower($email);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            Log::warning('Register rate limited.', [
                'email' => $email,
                'ip' => $request->ip(),
                'seconds_remaining' => $seconds,
            ]);

            return RegisterResult::RateLimited;
        }
        if (User::where('user_name', $user_name)->exists()) {
            RateLimiter::hit($rateLimitKey, 60);
            Log::warning('Register failed, username already taken.', [
                'user_name' => $user_name,
                'email' => $email,
                'ip' => $request->ip(),
            ]);

            return RegisterResult::UsernameTaken;
        }
        if (User::where('email', $email)->exists()) {
            RateLimiter::hit($rateLimitKey, 60);
            Log::warning('Register failed, email already taken.', [
                'user_name' => $user_name,
                'email' => $email,
                'ip' => $request->ip(),
            ]);

            return RegisterResult::EmailTaken;
        }
        try {
            User::create([
                'user_name' => $user_name,
                'email' => $email,
                'password' => $password,
            ]);
        } catch (QueryException $e) {
            if (! $this->isIntegrityConstraintViolation($e)) {
                throw $e;
            }
            if (User::where('user_name', $user_name)->exists()) {
                RateLimiter::hit($rateLimitKey, 60);
                Log::warning('Register failed, username already taken.', [
                    'user_name' => $user_name,
                    'email' => $email,
                    'ip' => $request->ip(),
                ]);

                return RegisterResult::UsernameTaken;
            }
            if (User::where('email', $email)->exists()) {
                RateLimiter::hit($rateLimitKey, 60);
                Log::warning('Register failed, email already taken.', [
                    'user_name' => $user_name,
                    'email' => $email,
                    'ip' => $request->ip(),
                ]);

                return RegisterResult::EmailTaken;
            }
            throw $e;
        }
        RateLimiter::clear($rateLimitKey);
        Log::info('User Registered.', [
            'user_name' => $user_name,
            'email' => $email,
            'ip' => $request->ip(),
        ]);

        return RegisterResult::Success;
    }

    private function rateLimitKey(string $email, Request $request): string
    {
        return Str::transliterate('register:'.$request->ip());
    }

    private function isIntegrityConstraintViolation(QueryException $e): bool
    {
        $sqlState = (string) ($e->errorInfo[0] ?? $e->getCode());

        return str_starts_with($sqlState, '23');
    }
}

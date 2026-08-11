<?php

namespace App\Actions\Auth;

use App\Enums\RegisterResult;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class RegisterUserAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(
        string $username,
        string $email,
        string $password,
        Request $request,
    ): RegisterResult {
        $rate_limit_key = $this->rateLimitKey($request);
        $username = Str::lower(Str::trim($username));
        $email = Str::lower(Str::trim($email));
        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Register rate limited.', [
                'email' => $email,
                'ip' => $request->ip(),
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return RegisterResult::RateLimited;
        }
        try {
            User::create([
                'username' => $username,
                'email' => $email,
                'password' => $password,
            ]);
        } catch (QueryException $e) {
            if (! $this->isIntegrityConstraintViolation($e)) {
                throw $e;
            }
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            if (User::where('username', $username)->exists()) {
                $this->logger->warning('Register failed, username already taken.', [
                    'username' => $username,
                    'email' => $email,
                    'ip' => $request->ip(),
                ]);

                return RegisterResult::UsernameTaken;
            }
            if (User::where('email', $email)->exists()) {
                $this->logger->warning('Register failed, email already taken.', [
                    'username' => $username,
                    'email' => $email,
                    'ip' => $request->ip(),
                ]);

                return RegisterResult::EmailTaken;
            }
            throw $e;
        }
        RateLimiter::clear($rate_limit_key);
        $this->logger->info('User registered.', [
            'username' => $username,
            'email' => $email,
            'ip' => $request->ip(),
        ]);

        return RegisterResult::Success;
    }

    private function rateLimitKey(Request $request): string
    {
        return Str::transliterate('register:'.$request->ip());
    }

    private function isIntegrityConstraintViolation(QueryException $e): bool
    {
        $sql_state = (string) ($e->errorInfo[0] ?? $e->getCode());

        return str_starts_with($sql_state, '23');
    }
}

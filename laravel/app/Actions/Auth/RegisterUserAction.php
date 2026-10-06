<?php

namespace App\Actions\Auth;

use App\Enums\RegisterResult;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Throwable;

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

        // Volume cap: every attempt (success or failure) counts and the bucket never clears —
        // account creation is the volume being capped (a row plus a queued mail each), so
        // clearing on success would defeat it. Login keeps clear-on-success because its bucket
        // counts failed credential guesses instead (contracts: .ai/rules/auth.md).
        RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);

        try {
            $user = User::create([
                'username' => $username,
                'email' => $email,
                'password' => $password,
            ]);
        } catch (QueryException $e) {
            if (! $this->isIntegrityConstraintViolation($e)) {
                throw $e;
            }
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
        $this->logger->info('User registered.', [
            'username' => $username,
            'email' => $email,
            'ip' => $request->ip(),
        ]);

        try {
            // create() only returns the inserted columns — reload the persisted row first,
            // otherwise locale is missing and preferredLocale() TypeErrors on a null.
            $user->refresh();
            $user->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            $this->logger->error('Verification email dispatch failed.', [
                'email' => $email,
                'ip' => $request->ip(),
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);
        }

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

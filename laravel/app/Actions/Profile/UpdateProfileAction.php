<?php

namespace App\Actions\Profile;

use App\Enums\UpdateProfileResult;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class UpdateProfileAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(
        User $user,
        string $username,
        ?string $firstname,
        ?string $lastname,
        ?string $gender,
        ?string $country,
        ?string $birthday,
        string $theme,
        string $locale,
        Request $request,
    ): UpdateProfileResult {
        abort_unless($user->is(auth()->user()), 403);

        $username = Str::lower(Str::trim($username));

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Profile update rate limited.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'seconds_remaining' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return UpdateProfileResult::RateLimited;
        }

        try {
            $user->update([
                'username' => $username,
                'firstname' => $this->nullableString($firstname),
                'lastname' => $this->nullableString($lastname),
                'theme' => $theme,
                'locale' => $locale,
                'gender' => $gender,
                'country' => $country,
                'birthday' => $birthday,
            ]);
        } catch (QueryException $e) {
            if (! $this->isIntegrityConstraintViolation($e)) {
                throw $e;
            }

            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Profile update failed, username already taken.', [
                'user_id' => $user->id,
                'attempted_username' => $username,
                'ip' => $request->ip(),
            ]);

            return UpdateProfileResult::UsernameTaken;
        }

        RateLimiter::clear($rate_limit_key);
        $this->logger->info('Profile updated.', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return UpdateProfileResult::Success;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('update-profile:'.$user->id.'|'.$request->ip());
    }

    private function nullableString(?string $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return filled($value) ? $value : null;
    }

    private function isIntegrityConstraintViolation(QueryException $e): bool
    {
        $sqlState = (string) ($e->errorInfo[0] ?? $e->getCode());

        return str_starts_with($sqlState, '23');
    }
}

<?php

namespace App\Actions\Profile;

use App\Enums\UpdateProfileResult;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class UpdateProfileAction
{
    public function execute(User $user, array $data, Request $request): UpdateProfileResult
    {
        $rateLimitKey = $this->rateLimitKey($user, $request);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            Log::warning('Profile update rate limited.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'seconds_remaining' => RateLimiter::availableIn($rateLimitKey),
            ]);

            return UpdateProfileResult::RateLimited;
        }
        $userName = $data['user_name'];
        if (
            $userName !== $user->user_name
            && User::query()
                ->where('user_name', $userName)
                ->whereKeyNot($user->id)
                ->exists()
        ) {
            RateLimiter::hit($rateLimitKey, 60);
            Log::warning('Profile update failed, username already taken.', [
                'user_id' => $user->id,
                'attempted_user_name' => $userName,
                'ip' => $request->ip(),
            ]);

            return UpdateProfileResult::UsernameTaken;
        }
        try {
            $user->update([
                'user_name' => $userName,
                'first_name' => $this->nullableString($data['first_name'] ?? null),
                'last_name' => $this->nullableString($data['last_name'] ?? null),
                'gender' => $data['gender'] ?? null,
                'country' => $data['country'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
            ]);
        } catch (QueryException $e) {
            if (! $this->isIntegrityConstraintViolation($e)) {
                throw $e;
            }
            if (
                User::query()
                    ->where('user_name', $userName)
                    ->whereKeyNot($user->id)
                    ->exists()
            ) {
                RateLimiter::hit($rateLimitKey, 60);
                Log::warning('Profile update failed, username already taken.', [
                    'user_id' => $user->id,
                    'attempted_user_name' => $userName,
                    'ip' => $request->ip(),
                ]);

                return UpdateProfileResult::UsernameTaken;
            }
            throw $e;
        }
        RateLimiter::hit($rateLimitKey, 60);
        Log::info('Profile updated.', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return UpdateProfileResult::Success;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('profile-update:'.$user->id.'|'.$request->ip());
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

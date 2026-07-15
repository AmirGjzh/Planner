<?php

namespace App\Actions\Auth;

use App\Enums\DeleteAccountResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

final class DeleteAccountAction
{
    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(User $user, string $password, Request $request): DeleteAccountResult
    {
        abort_unless($user->is(auth()->user()), 403);

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            Log::warning('Account deletion rate limited.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'seconds_remaining' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return DeleteAccountResult::RateLimited;
        }

        if (! Hash::check($password, $user->password)) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            Log::warning('Account deletion failed, wrong password.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return DeleteAccountResult::WrongPassword;
        }

        $userId = $user->id;
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $user->forceFill([
            'email' => 'deleted-user-'.$userId,
            'username' => 'deleted_user_'.$userId,
        ])->save();

        $user->delete();

        RateLimiter::clear($rate_limit_key);

        Log::info('User account deleted.', [
            'user_id' => $userId,
            'ip' => $request->ip(),
        ]);

        return DeleteAccountResult::Success;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('delete-account:'.$user->id.'|'.$request->ip());
    }
}

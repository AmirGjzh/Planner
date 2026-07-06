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

class DeleteAccountAction
{
    public function execute(User $user, string $password, Request $request): DeleteAccountResult
    {
        $rateLimitKey = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            Log::warning('Account deletion rate limited.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'seconds_remaining' => RateLimiter::availableIn($rateLimitKey),
            ]);

            return DeleteAccountResult::RateLimited;
        }

        if (! Hash::check($password, $user->password)) {
            RateLimiter::hit($rateLimitKey, 60);
            Log::warning('Account deletion failed, wrong password.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return DeleteAccountResult::WrongPassword;
        }

        $userId = $user->id;
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->forceFill([
            'email' => 'deleted-user-'.$userId,
            'user_name' => 'deleted_user_'.$userId,
        ])->save();

        $user->delete();

        RateLimiter::clear($rateLimitKey);

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

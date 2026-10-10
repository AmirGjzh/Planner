<?php

namespace App\Actions\Auth;

use App\Enums\DeleteAccountResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class DeleteAccountAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(User $user, string $password, Request $request): DeleteAccountResult
    {
        abort_unless($user->is(auth()->user()), 403);

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Account deletion rate limited.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return DeleteAccountResult::RateLimited;
        }

        if (! Hash::check($password, $user->password)) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Account deletion failed, wrong password.', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return DeleteAccountResult::WrongPassword;
        }

        $userId = $user->id;
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        Cookie::queue(Cookie::forget(LoginUserAction::SESSION_HINT));

        // Soft delete keeps the row so plans and tasks retain their owner; the
        // obfuscated email/username free the real values for reuse (the user-id
        // suffix keeps the unique indexes satisfied). The rest of the scrub
        // drops whatever secrets and PII the row still holds: the password
        // becomes junk no hash check can satisfy, the recaller token is
        // blanked, and name/birthday/gender are nulled — locale and theme
        // stay, preferences are not personal data.
        $user->forceFill([
            'email' => 'deleted-user-'.$userId,
            'username' => 'deleted_user_'.$userId,
            'password' => Str::random(60),
            'remember_token' => null,
            'firstname' => null,
            'lastname' => null,
            'birthday' => null,
            'gender' => null,
        ])->save();

        $user->delete();

        RateLimiter::clear($rate_limit_key);

        $this->logger->info('User account deleted.', [
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

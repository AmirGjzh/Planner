<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class ExpireSessionsOnPasswordChange
{
    /**
     * Kick every session that predates the latest password change, so a
     * reset acts as "sign out everywhere". Sessions without an auth_time
     * (remember-me logins, sessions from before this middleware) get
     * stamped on their first pass instead of being rejected. The
     * comparison is strictly greater-than: both timestamps are
     * second-precision, so a re-login in the same second as the change
     * survives — a `>=` would evict it on its very next request and loop.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $authTime = $request->session()->get('auth_time');

        if ($authTime === null) {
            $request->session()->put('auth_time', now()->toIso8601String());

            return $next($request);
        }

        if ($user->password_changed_at !== null
            && $user->password_changed_at->greaterThan(Carbon::parse($authTime))) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('toast', [
                    'title' => __('Your password was changed, sign in again.'),
                    'variant' => 'info',
                    'duration' => 6000,
                    'position' => 'bottom-center',
                ]);
        }

        return $next($request);
    }
}

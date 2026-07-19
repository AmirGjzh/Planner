<?php

namespace App\Actions\Auth;

use App\Enums\LogoutResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class LogoutUserAction
{
    public function execute(Request $request): LogoutResult
    {
        $userId = Auth::id();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info('User logged out.', [
            'user_id' => $userId,
            'ip' => $request->ip(),
        ]);

        return LogoutResult::Success;
    }
}

<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogoutUserAction
{
    public function execute(Request $request): void
    {
        $userId = Auth::id();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info('User logged out.', [
            'user_id' => $userId,
            'ip' => $request->ip(),
        ]);
    }
}

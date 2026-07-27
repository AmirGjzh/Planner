<?php

namespace App\Actions\Auth;

use App\Enums\LogoutResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Log\LoggerInterface;

final class LogoutUserAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(Request $request): LogoutResult
    {
        $userId = Auth::id();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $this->logger->info('User logged out.', [
            'user_id' => $userId,
            'ip' => $request->ip(),
        ]);

        return LogoutResult::Success;
    }
}

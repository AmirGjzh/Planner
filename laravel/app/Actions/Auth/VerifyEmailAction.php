<?php

namespace App\Actions\Auth;

use App\Enums\VerifyEmailResult;
use App\Models\User;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

final class VerifyEmailAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(User $user, Request $request): VerifyEmailResult
    {
        if ($user->hasVerifiedEmail()) {
            return VerifyEmailResult::AlreadyVerified;
        }

        $user->markEmailAsVerified();

        $this->logger->info('User email verified.', [
            'user_id' => $user->getKey(),
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return VerifyEmailResult::Verified;
    }
}

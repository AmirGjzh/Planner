<?php

namespace App\Actions\Task;

use App\Enums\ToggleTaskDoneResult;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class ToggleTaskDoneAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 20;

    private const int DECAY_SECONDS = 60;

    public function execute(User $user, Task $task, Request $request): ToggleTaskDoneResult
    {
        abort_unless($user->can('toggleDone', $task), 403);

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Task toggle rate limited.', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return ToggleTaskDoneResult::RateLimited;
        }

        $new_status = ! $task->done;

        $task->update(['done' => $new_status]);

        RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);

        $this->logger->info('Task toggled.', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'new_status' => $new_status,
        ]);

        return ToggleTaskDoneResult::Toggled;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('toggle-task:'.$user->id.'|'.$request->ip());
    }
}

<?php

namespace App\Actions\Task;

use App\Enums\ToggleTaskDoneResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ToggleTaskDoneAction
{
    public function execute(User $user, int $taskId, Request $request): ToggleTaskDoneResult
    {
        $key = 'toggle-task:'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 20)) {
            Log::warning('Task toggle rate limited.', [
                'user_id' => $user->id,
                'task_id' => $taskId,
                'seconds_remaining' => RateLimiter::availableIn($key),
            ]);

            return ToggleTaskDoneResult::RateLimited;
        }

        $task = $user->tasks()->findOrFail($taskId);

        abort_unless($user->can('toggleDone', $task), 403);

        $newStatus = ! $task->done;

        $task->update(['done' => $newStatus]);

        RateLimiter::hit($key, 60);

        Log::info('Task toggled.', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'new_status' => $newStatus,
        ]);

        return ToggleTaskDoneResult::Toggled;
    }
}

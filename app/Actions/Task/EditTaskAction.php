<?php

namespace App\Actions\Task;

use App\Enums\EditTaskResult;
use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class EditTaskAction
{
    public function execute(
        User $user,
        int $taskId,
        string $title,
        ?string $description,
        string $taskDate,
        int $estimatedMinutes,
        TaskPriority $priority,
        int $dayBeforeAlarm,
        int $categoryId,
        ?int $planId,
        Request $request,
    ): EditTaskResult {
        $task = $user->tasks()->findOrFail($taskId);

        abort_unless($user->can('update', $task), 403);

        $key = 'edit-task:'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Task edit rate limited.', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'seconds_remaining' => RateLimiter::availableIn($key),
            ]);

            return EditTaskResult::RateLimited;
        }

        if (! $user->categories()->whereKey($categoryId)->exists()) {
            RateLimiter::hit($key, 60);
            Log::warning('Task edit failed, invalid category.', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'category_id' => $categoryId,
            ]);

            return EditTaskResult::InvalidCategory;
        }

        if ($planId !== null && ! $user->plans()->whereKey($planId)->exists()) {
            RateLimiter::hit($key, 60);
            Log::warning('Task edit failed, invalid plan.', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'plan_id' => $planId,
            ]);

            return EditTaskResult::InvalidPlan;
        }

        $task->update([
            'title' => $title,
            'description' => $description,
            'task_date' => $taskDate,
            'estimated_minutes' => $estimatedMinutes,
            'priority' => $priority,
            'day_before_alarm' => $dayBeforeAlarm,
            'category_id' => $categoryId,
            'plan_id' => $planId,
        ]);

        RateLimiter::hit($key, 60);

        Log::info('Task updated.', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'title' => $title,
        ]);

        return EditTaskResult::Updated;
    }
}

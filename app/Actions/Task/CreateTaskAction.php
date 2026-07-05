<?php

namespace App\Actions\Task;

use App\Enums\CreateTaskResult;
use App\Enums\TaskPriority;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class CreateTaskAction
{
    public function execute(
        User $user,
        string $title,
        ?string $description,
        string $taskDate,
        int $estimatedMinutes,
        TaskPriority $priority,
        int $dayBeforeAlarm,
        int $categoryId,
        ?int $planId,
        Request $request,
    ): CreateTaskResult {
        abort_unless($user->can('create', Task::class), 403);

        $key = 'create-task:'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Task creation rate limited.', [
                'user_id' => $user->id,
                'seconds_remaining' => RateLimiter::availableIn($key),
            ]);

            return CreateTaskResult::RateLimited;
        }

        if (! $user->categories()->whereKey($categoryId)->exists()) {
            RateLimiter::hit($key, 60);
            Log::warning('Task creation failed, invalid category.', [
                'user_id' => $user->id,
                'category_id' => $categoryId,
            ]);

            return CreateTaskResult::InvalidCategory;
        }

        if ($planId !== null && ! $user->plans()->whereKey($planId)->exists()) {
            RateLimiter::hit($key, 60);
            Log::warning('Task creation failed, invalid plan.', [
                'user_id' => $user->id,
                'plan_id' => $planId,
            ]);

            return CreateTaskResult::InvalidPlan;
        }

        $user->tasks()->create([
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

        Log::info('Task created.', [
            'user_id' => $user->id,
            'title' => $title,
            'task_date' => $taskDate,
            'estimated_minutes' => $estimatedMinutes,
        ]);

        return CreateTaskResult::Created;
    }
}

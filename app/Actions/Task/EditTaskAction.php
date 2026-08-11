<?php

namespace App\Actions\Task;

use App\Enums\EditTaskResult;
use App\Enums\TaskPriority;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class EditTaskAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(
        User $user,
        Task $task,
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
        abort_unless($user->can('update', $task), 403);

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Task edit rate limited.', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return EditTaskResult::RateLimited;
        }

        if (! $user->categories()->whereKey($categoryId)->exists()) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Task edit failed, invalid category.', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'category_id' => $categoryId,
            ]);

            return EditTaskResult::InvalidCategory;
        }

        if ($planId !== null && ! $user->plans()->whereKey($planId)->exists()) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Task edit failed, invalid plan.', [
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

        RateLimiter::clear($rate_limit_key);

        $this->logger->info('Task updated.', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'title' => $title,
        ]);

        return EditTaskResult::Updated;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('edit-task:'.$user->id.'|'.$request->ip());
    }
}

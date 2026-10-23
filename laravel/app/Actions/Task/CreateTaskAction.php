<?php

namespace App\Actions\Task;

use App\Enums\CreateTaskResult;
use App\Enums\TaskPriority;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class CreateTaskAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

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

        // Str::squish trims the edges (Str::trim's charlist covers ZWNJ — a stray half-space
        // can't render the title invisible) and collapses internal whitespace runs to one
        // space. No case folding: the casing the user typed is what gets stored (user decision).
        $title = Str::squish($title);

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Task creation rate limited.', [
                'user_id' => $user->id,
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return CreateTaskResult::RateLimited;
        }

        if (! $user->categories()->whereKey($categoryId)->exists()) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Task creation failed, invalid category.', [
                'user_id' => $user->id,
                'category_id' => $categoryId,
            ]);

            return CreateTaskResult::InvalidCategory;
        }

        $plan = $planId === null ? null : $user->plans()->whereKey($planId)->first();

        if ($planId !== null && $plan === null) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Task creation failed, invalid plan.', [
                'user_id' => $user->id,
                'plan_id' => $planId,
            ]);

            return CreateTaskResult::InvalidPlan;
        }

        // A task's date must lie inside its plan's range; ISO Y-m-d compares as strings (R2).
        // The plan casts are Carbon — compare against the plain date form, not the midnight string.
        if ($plan !== null && ($taskDate < $plan->start_date->toDateString() || $taskDate > $plan->finish_date->toDateString())) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Task creation failed, date outside plan range.', [
                'user_id' => $user->id,
                'plan_id' => $planId,
                'task_date' => $taskDate,
            ]);

            return CreateTaskResult::DateOutOfRange;
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

        // A completed plan must not contain undone tasks — adding one reopens it (R1).
        if ($plan?->done) {
            $plan->update(['done' => false]);

            $this->logger->info('Plan re activated, undone task added.', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);
        }

        RateLimiter::clear($rate_limit_key);

        $this->logger->info('Task created.', [
            'user_id' => $user->id,
            'title' => $title,
            'task_date' => $taskDate,
            'estimated_minutes' => $estimatedMinutes,
        ]);

        return CreateTaskResult::Created;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('create-task:'.$user->id.'|'.$request->ip());
    }
}

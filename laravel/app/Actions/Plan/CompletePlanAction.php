<?php

namespace App\Actions\Plan;

use App\Enums\CompletePlanResult;
use App\Models\Plan;
use App\Models\User;
use Psr\Log\LoggerInterface;

final class CompletePlanAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(User $user, Plan $plan): CompletePlanResult
    {
        abort_unless($user->can('update', $plan), 403);

        if ($plan->tasks()->where('done', false)->exists()) {
            $this->logger->warning('Plan completion blocked, has undone tasks.', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'name' => $plan->name,
            ]);

            return CompletePlanResult::HasUndoneTasks;
        }

        $plan->update(['done' => true]);

        $this->logger->info('Plan completed.', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $plan->name,
        ]);

        return CompletePlanResult::Completed;
    }
}

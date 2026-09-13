<?php

namespace App\Actions\Plan;

use App\Enums\DeletePlanResult;
use App\Models\Plan;
use App\Models\User;
use Psr\Log\LoggerInterface;

final class DeletePlanAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(User $user, Plan $plan): DeletePlanResult
    {
        abort_unless($user->can('delete', $plan), 403);

        $tasks_count = $plan->tasks()->count();

        if ($tasks_count > 0) {
            $this->logger->warning('Plan deletion failed, has tasks assigned.', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'name' => $plan->name,
                'tasks_count' => $tasks_count,
            ]);

            return DeletePlanResult::HasTasks;
        }

        $plan->delete();

        $this->logger->info('Plan deleted.', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $plan->name,
        ]);

        return DeletePlanResult::Deleted;
    }
}

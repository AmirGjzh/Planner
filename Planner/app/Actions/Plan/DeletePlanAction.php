<?php

namespace App\Actions\Plan;

use App\Enums\DeletePlanResult;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DeletePlanAction
{
    public function execute(User $user, int $planId): DeletePlanResult
    {
        $plan = $user->plans()->findOrFail($planId);

        abort_unless($user->can('delete', $plan), 403);

        $tasksCount = $plan->tasks()->count();

        if ($tasksCount > 0) {
            Log::warning('Plan deletion failed, has tasks assigned.', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'name' => $plan->name,
                'tasks_count' => $tasksCount,
            ]);

            return DeletePlanResult::HasTasks;
        }

        $plan->delete();

        Log::info('Plan deleted.', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $plan->name,
        ]);

        return DeletePlanResult::Deleted;
    }
}

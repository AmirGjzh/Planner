<?php

namespace App\Actions\Plan;

use App\Models\Plan;
use App\Models\User;
use Psr\Log\LoggerInterface;

final class ReopenPlanAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(User $user, Plan $plan): void
    {
        abort_unless($user->can('update', $plan), 403);

        $plan->update(['done' => false]);

        $this->logger->info('Plan reopened.', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $plan->name,
        ]);
    }
}

<?php

namespace App\Actions\Plan;

use App\Enums\EditPlanResult;
use App\Models\User;
use App\View\Components\DateRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class EditPlanAction
{
    public function execute(User $user, int $planId, string $name, ?string $description, DateRange $range, Request $request): EditPlanResult
    {
        $plan = $user->plans()->findOrFail($planId);

        abort_unless($user->can('update', $plan), 403);

        $key = 'edit-plan:'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Plan edit rate limited.', [
                'user_id' => $user->id,
                'seconds_remaining' => RateLimiter::availableIn($key),
            ]);

            return EditPlanResult::RateLimited;
        }

        $existing = $user->plans()
            ->where('name', $name)
            ->where('id', '!=', $plan->id)
            ->first();

        if ($existing) {
            RateLimiter::hit($key, 60);
            Log::warning('Plan edit failed, already exists.', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'name' => $name,
            ]);

            return EditPlanResult::AlreadyExists;
        }

        $plan->update([
            'name' => $name,
            'description' => $description,
            'start_date' => $range->getStart(),
            'finish_date' => $range->getEnd(),
        ]);

        RateLimiter::hit($key, 60);

        Log::info('Plan updated.', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $name,
            'start_date' => $range->getStart(),
            'finish_date' => $range->getEnd(),
        ]);

        return EditPlanResult::Updated;
    }
}

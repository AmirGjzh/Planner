<?php

namespace App\Actions\Plan;

use App\Enums\CreatePlanResult;
use App\Models\Plan;
use App\Models\User;
use App\View\Components\DateRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class CreatePlanAction
{
    public function execute(User $user, string $name, ?string $description, DateRange $range, Request $request): CreatePlanResult
    {
        abort_unless($user->can('create', Plan::class), 403);

        $key = 'create-plan:'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Plan creation rate limited.', [
                'user_id' => $user->id,
                'seconds_remaining' => RateLimiter::availableIn($key),
            ]);

            return CreatePlanResult::RateLimited;
        }

        if ($user->plans()->where('name', $name)->exists()) {
            RateLimiter::hit($key, 60);
            Log::warning('Plan creation failed, already exists.', [
                'user_id' => $user->id,
                'name' => $name,
            ]);

            return CreatePlanResult::AlreadyExists;
        }

        $user->plans()->create([
            'name' => $name,
            'description' => $description,
            'start_date' => $range->getStart(),
            'finish_date' => $range->getEnd(),
        ]);

        RateLimiter::hit($key, 60);

        Log::info('Plan created.', [
            'user_id' => $user->id,
            'name' => $name,
            'start_date' => $range->getStart(),
            'finish_date' => $range->getEnd(),
        ]);

        return CreatePlanResult::Created;
    }
}

<?php

namespace App\Actions\Dashboard;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class AttentionTasksAction
{
    public function execute(User $user, ?Carbon $today = null): Collection
    {
        $today = ($today ?? now())->startOfDay();

        return $user->tasks()
            ->where('done', false)
            ->get()
            ->filter(fn ($task) => $today->gte(Carbon::parse($task->task_date)->subDays($task->day_before_alarm)))
            ->sortBy('task_date')
            ->values();
    }
}

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

        // Bounded scan: a task can only enter its window within day_before_alarm's validated
        // max (365), so anything beyond today + 365 is provably out of window and the exact
        // per-task math below trims the rest (portable across SQLite/MariaDB — no date-interval
        // SQL). Ordering happens in SQL with an id tiebreaker so same-date cards keep a stable,
        // deterministic order.
        return $user->tasks()
            ->where('done', false)
            ->where('task_date', '<=', $today->copy()->addDays(365)->toDateString())
            ->orderBy('task_date')
            ->orderBy('id')
            ->get()
            ->filter(fn ($task) => $today->gte(Carbon::parse($task->task_date)->subDays($task->day_before_alarm)))
            ->values();
    }
}

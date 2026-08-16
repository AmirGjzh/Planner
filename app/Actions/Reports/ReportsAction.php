<?php

namespace App\Actions\Reports;

use App\Models\User;
use App\Support\Minutes;
use Carbon\Carbon;

final class ReportsAction
{
    public function summary(User $user, string $start, string $end): array
    {
        [$start, $end] = $this->orderedRange($start, $end);

        $tasks = $user->tasks()
            ->whereBetween('task_date', [$start, $end])
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(SUM(CASE WHEN done = 1 THEN 1 ELSE 0 END), 0) as completed,
                COALESCE(SUM(estimated_minutes), 0) as minutes
            ')
            ->first();

        $plans = $user->plans()
            ->whereDate('start_date', '<=', $end)
            ->whereDate('finish_date', '>=', $start)
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(SUM(CASE WHEN done = 1 THEN 1 ELSE 0 END), 0) as completed
            ')
            ->first();

        $totalTasks = (int) $tasks->total;

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => (int) $tasks->completed,
            'completion_rate' => $totalTasks > 0 ? round(($tasks->completed / $totalTasks) * 100) : 0,
            'estimated_time' => Minutes::format((int) $tasks->minutes),
            'total_plans' => (int) $plans->total,
            'completed_plans' => (int) $plans->completed,
        ];
    }

    public function chart(User $user, string $start, string $end): array
    {
        [$start, $end] = $this->orderedRange($start, $end);

        return $user->tasks()
            ->whereBetween('task_date', [$start, $end])
            ->selectRaw('
                DATE(task_date) as day,
                SUM(CASE WHEN done = 1 THEN estimated_minutes ELSE 0 END) as completed,
                SUM(CASE WHEN done = 0 THEN estimated_minutes ELSE 0 END) as remaining
            ')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($row) => [
                'label' => Carbon::parse($row->day)->format('M j'),
                'completed' => (int) $row->completed,
                'remaining' => (int) $row->remaining,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function orderedRange(string $start, string $end): array
    {
        return $start > $end ? [$end, $start] : [$start, $end];
    }
}

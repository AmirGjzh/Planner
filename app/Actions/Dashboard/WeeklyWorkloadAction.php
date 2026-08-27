<?php

namespace App\Actions\Dashboard;

use App\Enums\WorkloadLevel;
use App\Models\User;
use App\Support\Jalali;
use App\Support\Minutes;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class WeeklyWorkloadAction
{
    public function execute(User $user, ?Carbon $today = null): array
    {
        $today = ($today ?? now())->startOfDay();
        $weekStartDay = app()->isLocale('fa') ? Carbon::SATURDAY : Carbon::SUNDAY;
        $start = $today->copy()->startOfWeek($weekStartDay);

        $totals = $user->tasks()
            ->whereBetween('task_date', [$start->toDateString(), $start->copy()->addDays(6)->toDateString()])
            ->selectRaw('task_date, SUM(estimated_minutes) as total')
            ->groupBy('task_date')
            ->pluck('total', 'task_date');

        return collect(range(0, 6))
            ->map(fn (int $offset) => $this->dayCell($start->copy()->addDays($offset), $today, $totals))
            ->all();
    }

    private function dayCell(Carbon $date, Carbon $today, Collection $totals): array
    {
        $minutes = (int) ($totals[$date->toDateString()] ?? 0);
        $isFa = app()->isLocale('fa');

        return [
            'day' => $isFa ? Jalali::format($date, 'EEEE') : $date->format('D'),
            'date' => $isFa ? Jalali::format($date, 'd MMM') : $date->format('M j'),
            'is_today' => $date->isSameDay($today),
            'past' => $date->isBefore($today),
            'minutes' => $minutes,
            'formatted' => Minutes::format($minutes),
            'level' => WorkloadLevel::forMinutes($minutes)->value,
        ];
    }
}

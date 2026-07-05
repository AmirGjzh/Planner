<?php

use App\Models\Task;
use App\Models\User;
use App\View\Components\DateRange;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $userId;

    public DateRange $date_filter;

    public function mount()
    {
        $this->userId = auth()->id() ?? abort(403);
        $this->date_filter = DateRange::thisMonth();
    }

    #[Computed]
    public function user(): User
    {
        return User::query()->findOrFail($this->userId);
    }

    #[Computed]
    public function stats(): array
    {
        $query = Task::query()->where('user_id', $this->userId);

        if ($this->date_filter->hasStart()) {
            $query->where('task_date', '>=', $this->date_filter->getStart());
        }

        if ($this->date_filter->hasEnd()) {
            $query->where('task_date', '<=', $this->date_filter->getEnd());
        }

        $createdQuery = clone $query;
        $tasksCreated = $createdQuery->count();

        $completedQuery = clone $query;
        $tasksCompleted = (clone $completedQuery)->where('done', true)->count();

        $completionRate = $tasksCreated > 0
            ? round(($tasksCompleted / $tasksCreated) * 100)
            : 0;

        $overdueQuery = clone $query;
        $overdueCount = (clone $overdueQuery)
            ->where('done', false)
            ->where('task_date', '<', now()->format('Y-m-d'))
            ->count();

        return [
            'tasks_created' => $tasksCreated,
            'tasks_completed' => $tasksCompleted,
            'completion_rate' => $completionRate,
            'overdue_count' => $overdueCount,
        ];
    }

    public function applyDateFilter(): void
    {
        unset($this->stats);
    }
};

<?php

use App\Models\Task;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $userId;

    public function mount()
    {
        $this->userId = auth()->id() ?? abort(403);
    }

    #[Computed]
    public function user(): User
    {
        return User::query()->findOrFail($this->userId);
    }

    #[Computed]
    public function upcomingTasks()
    {
        $today = now()->format('Y-m-d');

        return Task::query()
            ->with('category', 'plan')
            ->where('user_id', $this->userId)
            ->where('task_date', '>=', $today)
            ->whereRaw("DATE(task_date, '-' || day_before_alarm || ' days') <= ?", [$today])
            ->orderBy('task_date')
            ->get();
    }
};

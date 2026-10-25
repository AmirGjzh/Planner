<?php

use App\Actions\Dashboard\AttentionTasksAction;
use App\Actions\Dashboard\WeeklyWorkloadAction;
use App\Livewire\Concerns\HasUser;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use HasUser;

    public function mount(): void
    {
        if ($toast = session()->pull('toast')) {
            $this->dispatch('toast', ...$toast);
        }
    }

    #[Computed]
    public function attentionTasks(): Collection
    {
        return app(AttentionTasksAction::class)->execute($this->user);
    }

    #[Computed]
    public function week(): array
    {
        return app(WeeklyWorkloadAction::class)->execute($this->user);
    }
};

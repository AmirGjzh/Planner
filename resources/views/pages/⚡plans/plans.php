<?php

use App\Actions\Plan\CompletePlanAction;
use App\Actions\Plan\CreatePlanAction;
use App\Actions\Plan\DeletePlanAction;
use App\Actions\Plan\EditPlanAction;
use App\Actions\Plan\ReopenPlanAction;
use App\Enums\CompletePlanResult;
use App\Enums\CreatePlanResult;
use App\Enums\DeletePlanResult;
use App\Enums\EditPlanResult;
use App\Livewire\Concerns\HasUser;
use App\Models\Plan;
use App\Support\Jalali;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use HasUser, WithPagination;

    public string $add_name = '';

    public ?string $add_description = null;

    public ?array $add_range = null;

    public ?string $add_error = null;

    public ?int $editing_id = null;

    public string $edit_name = '';

    public ?string $edit_description = null;

    public ?array $edit_range = null;

    public ?string $edit_error = null;

    public ?int $deleting_id = null;

    public ?string $delete_error = null;

    public ?int $completing_id = null;

    public ?string $complete_error = null;

    public ?int $reopening_id = null;

    public ?array $range_filter = null;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'state';

    #[Url]
    public string $status_filter = 'all';

    public function mount(): void
    {
        $this->range_filter = $this->defaultRangeFilter();
    }

    #[Computed]
    public function plans()
    {
        return Plan::query()
            ->select(['id', 'name', 'description', 'start_date', 'finish_date', 'done', 'user_id', 'created_at'])
            ->where('user_id', auth()->id())
            ->when($this->search, fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', [Str::lower('%'.$this->search.'%')]))
            ->when($this->range_filter['start'] ?? null, fn ($q) => $q->whereDate('finish_date', '>=', $this->range_filter['start']))
            ->when($this->range_filter['end'] ?? null, fn ($q) => $q->whereDate('start_date', '<=', $this->range_filter['end']))
            ->withCount([
                'tasks',
                'tasks as tasks_done_count' => fn ($q) => $q->where('done', true),
            ])
            ->withSum('tasks', 'estimated_minutes')
            ->when($this->status_filter === 'active', fn ($q) => $q->where('done', false)->whereDate('finish_date', '>=', now()))
            ->when($this->status_filter === 'completed', fn ($q) => $q->where('done', true))
            ->when($this->status_filter === 'overdue', fn ($q) => $q->where('done', false)->whereDate('finish_date', '<', now()))
            ->when($this->sort === 'name', fn ($q) => $q->orderBy('name'))
            ->when($this->sort === 'deadline', fn ($q) => $q->orderBy('finish_date')->orderBy('id'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest())
            ->when($this->sort === 'load', fn ($q) => $q->orderByDesc('tasks_sum_estimated_minutes'))
            ->when($this->sort === 'state', fn ($q) => $q->orderByRaw('CASE WHEN done = 0 AND finish_date >= ? THEN 0 WHEN done = 0 THEN 1 ELSE 2 END', [now()->toDateString()]))
            ->paginate(3)->onEachSide(1);
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        if ($this->search !== '' || $this->status_filter !== 'all') {
            return true;
        }

        $bounds = $this->defaultRangeFilter();

        return ($this->range_filter['start'] ?? null) !== null
            && (($this->range_filter['start'] ?? null) !== $bounds['start']
                || ($this->range_filter['end'] ?? null) !== $bounds['end']);
    }

    /**
     * @return array{start: string, end: string}
     */
    private function defaultRangeFilter(): array
    {
        if (app()->isLocale('fa')) {
            return Jalali::monthBounds(now());
        }

        return [
            'start' => now()->startOfMonth()->format('Y-m-d'),
            'end' => now()->endOfMonth()->format('Y-m-d'),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingRangeFilter(): void
    {
        $this->resetPage();
    }

    public function addPlan(CreatePlanAction $action): void
    {
        $this->validate([
            'add_name' => $this->rules()['add_name'],
            'add_description' => $this->rules()['add_description'],
            'add_range' => $this->rules()['add_range'],
            'add_range.start' => $this->rules()['add_range.start'],
            'add_range.end' => $this->rules()['add_range.end'],
        ]);

        $result = $action->execute($this->user, $this->add_name, $this->add_description, $this->add_range, request());

        $this->add_error = match ($result) {
            CreatePlanResult::AlreadyExists => 'already_exists',
            CreatePlanResult::RateLimited => 'rate_limited',
            CreatePlanResult::Created => null,
        };

        if ($this->add_error) {
            return;
        }

        $this->add_name = '';
        $this->add_description = null;
        $this->add_range = null;
        unset($this->plans);
        $this->dispatch('close-modal',
            id: 'add-plan-form'
        );
        $this->dispatch('toast',
            title: __('Your plan created'),
            variant: 'success',
            duration: 3000,
            position: 'bottom-center'
        );
    }

    public function cancelAdd(): void
    {
        $this->add_error = null;
        $this->add_name = '';
        $this->add_description = null;
        $this->add_range = null;
        $this->resetValidation();
    }

    public function editPlan(EditPlanAction $action): void
    {
        $this->validate([
            'edit_name' => $this->rules()['edit_name'],
            'edit_description' => $this->rules()['edit_description'],
            'edit_range' => $this->rules()['edit_range'],
            'edit_range.start' => $this->rules()['edit_range.start'],
            'edit_range.end' => $this->rules()['edit_range.end'],
        ]);

        $plan = $this->user->plans()->findOrFail($this->editing_id);
        $result = $action->execute($this->user, $plan, $this->edit_name, $this->edit_description, $this->edit_range, request());

        $this->edit_error = match ($result) {
            EditPlanResult::AlreadyExists => 'already_exists',
            EditPlanResult::RateLimited => 'rate_limited',
            EditPlanResult::Updated => null,
        };

        if ($this->edit_error) {
            return;
        }

        $this->edit_name = '';
        $this->edit_description = null;
        $this->edit_range = null;
        $this->editing_id = null;
        unset($this->plans);
        $this->dispatch('close-modal',
            id: 'edit-plan-form'
        );
        $this->dispatch('toast',
            title: __('Your plan updated'),
            variant: 'info',
            duration: 3000,
            position: 'bottom-center'
        );
    }

    public function cancelEdit(): void
    {
        $this->edit_error = null;
        $this->edit_name = '';
        $this->edit_description = null;
        $this->edit_range = null;
        $this->editing_id = null;
        $this->resetValidation();
    }

    public function deletePlan(DeletePlanAction $action): void
    {
        $plan = $this->user->plans()->findOrFail($this->deleting_id);

        $result = $action->execute($this->user, $plan);

        $this->delete_error = match ($result) {
            DeletePlanResult::HasTasks => 'has_tasks',
            DeletePlanResult::Deleted => null,
        };

        if ($result === DeletePlanResult::Deleted) {
            $this->deleting_id = null;
            unset($this->plans);
            $this->dispatch('close-modal', id: 'delete-plan-confirmation');
            $this->dispatch('toast',
                title: __('Your plan deleted'),
                variant: 'info',
                duration: 3000,
                position: 'bottom-center'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->delete_error = null;
        $this->deleting_id = null;
        $this->resetValidation();
    }

    public function completePlan(CompletePlanAction $action): void
    {
        $plan = $this->user->plans()->findOrFail($this->completing_id);

        $result = $action->execute($this->user, $plan);

        $this->complete_error = match ($result) {
            CompletePlanResult::HasUndoneTasks => 'has_undone_tasks',
            CompletePlanResult::Completed => null,
        };

        if ($result === CompletePlanResult::Completed) {
            $this->completing_id = null;
            unset($this->plans);
            $this->dispatch('close-modal', id: 'complete-plan-confirmation');
            $this->dispatch('toast',
                title: __('Your plan completed'),
                variant: 'info',
                duration: 3000,
                position: 'bottom-center'
            );
        }
    }

    public function cancelComplete(): void
    {
        $this->complete_error = null;
        $this->completing_id = null;
        $this->resetValidation();
    }

    public function reopenPlan(ReopenPlanAction $action): void
    {
        $plan = $this->user->plans()->findOrFail($this->reopening_id);

        $action->execute($this->user, $plan);

        $this->reopening_id = null;
        unset($this->plans);
        $this->dispatch('close-modal', id: 'reopen-plan-confirmation');
        $this->dispatch('toast',
            title: __('Your plan reactivated'),
            variant: 'info',
            duration: 3000,
            position: 'bottom-center'
        );
    }

    public function cancelReopen(): void
    {
        $this->reopening_id = null;
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'add_name' => ['required', 'string', 'max:255'],
            'add_description' => ['nullable', 'string', 'max:5000'],
            'add_range' => ['required', 'array'],
            'add_range.start' => ['required', 'date_format:Y-m-d'],
            'add_range.end' => ['required', 'date_format:Y-m-d', 'after:add_range.start'],
            'edit_name' => ['required', 'string', 'max:255'],
            'edit_description' => ['nullable', 'string', 'max:5000'],
            'edit_range' => ['required', 'array'],
            'edit_range.start' => ['required', 'date_format:Y-m-d'],
            'edit_range.end' => ['required', 'date_format:Y-m-d', 'after:edit_range.start'],
        ];
    }

    protected function messages(): array
    {
        return [
            'add_name.required' => __('Plan name is required.'),
            'add_name.max' => __('Plan name cannot exceed 255 characters.'),
            'add_description.max' => __('Plan description cannot exceed 5000 characters.'),
            'add_range.required' => __('Plan range is required.'),
            'add_range.start.required' => __('Start date is required.'),
            'add_range.end.required' => __('End date is required.'),
            'add_range.end.after' => __('End date must be after the start date.'),
            'edit_name.required' => __('Plan name is required.'),
            'edit_name.max' => __('Plan name cannot exceed 255 characters.'),
            'edit_description.max' => __('Plan description cannot exceed 5000 characters.'),
            'edit_range.required' => __('Plan range is required.'),
            'edit_range.start.required' => __('Start date is required.'),
            'edit_range.end.required' => __('End date is required.'),
            'edit_range.end.after' => __('End date must be after the start date.'),
        ];
    }
};

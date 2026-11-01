<?php

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\EditTaskAction;
use App\Actions\Task\ToggleTaskDoneAction;
use App\Enums\CreateTaskResult;
use App\Enums\DeleteTaskResult;
use App\Enums\EditTaskResult;
use App\Enums\TaskPriority;
use App\Enums\ToggleTaskDoneResult;
use App\Livewire\Concerns\HasUser;
use App\Models\Category;
use App\Models\Plan;
use App\Models\Task;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app', ['title' => 'Tasks'])] class extends Component
{
    use HasUser, WithPagination;

    public string $add_title = '';

    public ?string $add_description = null;

    public string $add_date = '';

    public ?int $add_estimated_minutes = null;

    public ?int $add_alarm_days = null;

    public ?string $add_priority = null;

    public ?int $add_category_id = null;

    public ?int $add_plan_id = null;

    public ?string $add_error = null;

    public ?int $editing_id = null;

    public string $edit_title = '';

    public ?string $edit_description = null;

    public string $edit_date = '';

    public int $edit_estimated_minutes = 0;

    public int $edit_alarm_days = 0;

    public string $edit_priority = 'medium';

    public ?int $edit_category_id = null;

    public ?int $edit_plan_id = null;

    public ?string $edit_error = null;

    public ?int $deleting_id = null;

    public ?int $completing_id = null;

    public ?string $complete_error = null;

    public ?int $reopening_id = null;

    public ?string $reopen_error = null;

    public ?array $range_filter = null;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'state';

    #[Url]
    public string $status_filter = 'all';

    #[Url]
    public array $category_filter = [];

    #[Url]
    public array $plan_filter = [];

    #[Url(as: 'add_plan', history: false)]
    public ?int $addPlan = null;

    public function mount(): void
    {
        if ($toast = session()->pull('toast')) {
            $this->dispatch('toast', ...$toast);
        }

        $this->add_date = now()->format('Y-m-d');

        $this->range_filter = [
            'start' => now()->today()->format('Y-m-d'),
            'end' => now()->today()->format('Y-m-d'),
        ];

        if (request()->filled('search') || request()->filled('category_filter') || request()->filled('plan_filter')) {
            $this->range_filter = null;
        }

        if ($this->addPlan !== null) {
            $plan = Plan::query()->where('user_id', auth()->id())->find($this->addPlan);

            if ($plan) {
                $this->dispatch('open-modal', id: 'add-task-form');
                $this->add_plan_id = $plan->id;
            }

            $this->addPlan = null;
        }
    }

    #[Computed]
    public function tasks()
    {
        $userId = auth()->id();
        $today = now();
        $search = Str::squish($this->search);

        return Task::query()
            ->select(['id', 'title', 'description', 'task_date', 'estimated_minutes', 'priority', 'done', 'day_before_alarm', 'plan_id', 'category_id', 'user_id', 'created_at'])
            ->with('category:id,name', 'plan:id,name')
            ->where('user_id', $userId)
            ->when($search, fn ($q) => $q->whereRaw('LOWER(title) LIKE ?', [Str::lower('%'.$search.'%')]))
            ->when($this->range_filter['start'] ?? null, fn ($q) => $q->where('task_date', '>=', $this->range_filter['start']))
            ->when($this->range_filter['end'] ?? null, fn ($q) => $q->where('task_date', '<=', $this->range_filter['end']))
            ->when($this->status_filter === 'active', fn ($q) => $q->where('done', false)->where('task_date', '>=', $today->toDateString()))
            ->when($this->status_filter === 'completed', fn ($q) => $q->where('done', true))
            ->when($this->status_filter === 'overdue', fn ($q) => $q->where('done', false)->where('task_date', '<', $today->toDateString()))
            ->when($this->category_filter, fn ($q) => $q->whereIn('category_id', $this->category_filter))
            ->when($this->plan_filter, fn ($q) => $q->whereIn('plan_id', $this->plan_filter))
            // Deterministic tiebreakers (plans precedent): ties would otherwise shuffle between
            // pages; an unknown ?sort= falls back to orderBy('id') instead of no ORDER BY.
            ->when($this->sort === 'date', fn ($q) => $q->orderBy('task_date')->orderBy('id'))
            ->when($this->sort === 'priority', fn ($q) => $q->orderByRaw("CASE priority WHEN 'high' THEN 0 WHEN 'medium' THEN 1 WHEN 'low' THEN 2 ELSE 3 END")->orderBy('id'))
            ->when(in_array($this->sort, ['estimated', 'load'], true), fn ($q) => $q->orderByDesc('estimated_minutes')->orderBy('id'))
            ->when($this->sort === 'state', fn ($q) => $q->orderByRaw('CASE WHEN done = 0 AND task_date >= ? THEN 0 WHEN done = 0 THEN 1 ELSE 2 END', [$today->toDateString()])->orderBy('task_date')->orderBy('id'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest()->orderByDesc('id'))
            ->when(! in_array($this->sort, ['date', 'priority', 'estimated', 'load', 'state', 'latest'], true), fn ($q) => $q->orderBy('id'))
            ->paginate(6)->onEachSide(1);
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->select(['id', 'name'])
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function plans()
    {
        return Plan::query()
            ->select(['id', 'name'])
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        if ($this->search !== '' || $this->status_filter !== 'all' || $this->category_filter !== [] || $this->plan_filter !== []) {
            return true;
        }

        $today = now()->today()->format('Y-m-d');

        return ($this->range_filter['start'] ?? null) !== null
            && (($this->range_filter['start'] ?? null) !== $today
                || ($this->range_filter['end'] ?? null) !== $today);
    }

    public function updatingAddPlanId(mixed &$value): void
    {
        $value = ($value === '' || $value === 'null') ? null : $value;
    }

    public function updatingEditPlanId(mixed &$value): void
    {
        $value = ($value === '' || $value === 'null') ? null : $value;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPlanFilter(): void
    {
        $this->resetPage();
    }

    public function updatingRangeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function addTask(CreateTaskAction $action): void
    {
        $this->add_title = Str::squish($this->add_title);

        try {
            $this->validate([
                'add_title' => $this->rules()['add_title'],
                'add_description' => $this->rules()['add_description'],
                'add_date' => $this->rules()['add_date'],
                'add_estimated_minutes' => $this->rules()['add_estimated_minutes'],
                'add_alarm_days' => $this->rules()['add_alarm_days'],
                'add_priority' => $this->rules()['add_priority'],
                'add_category_id' => $this->rules()['add_category_id'],
                'add_plan_id' => $this->rules()['add_plan_id'],
            ]);
        } catch (ValidationException $exception) {
            $this->dispatch('focus-first-error');

            throw $exception;
        }

        $result = $action->execute(
            $this->user,
            $this->add_title,
            $this->add_description,
            $this->add_date,
            $this->add_estimated_minutes,
            TaskPriority::from($this->add_priority),
            $this->add_alarm_days,
            $this->add_category_id,
            $this->add_plan_id,
            request(),
        );

        $this->add_error = match ($result) {
            CreateTaskResult::RateLimited => 'rate_limited',
            CreateTaskResult::InvalidCategory => 'invalid_category',
            CreateTaskResult::InvalidPlan => 'invalid_plan',
            CreateTaskResult::DateOutOfRange => 'date_out_of_range',
            CreateTaskResult::Created => null,
        };

        if ($this->add_error) {
            return;
        }

        $this->add_title = '';
        $this->add_description = null;
        $this->add_date = now()->format('Y-m-d');
        $this->add_estimated_minutes = null;
        $this->add_alarm_days = null;
        $this->add_priority = null;
        $this->add_category_id = null;
        $this->add_plan_id = null;
        unset($this->tasks);
        $this->dispatch('close-modal', id: 'add-task-form');
        $this->dispatch('toast',
            title: __('Your task created successfully'),
            variant: 'success',
            duration: 3000,
            position: 'bottom-center'
        );
    }

    public function cancelAdd(): void
    {
        $this->add_error = null;
        $this->add_title = '';
        $this->add_description = null;
        $this->add_date = now()->format('Y-m-d');
        $this->add_estimated_minutes = null;
        $this->add_alarm_days = null;
        $this->add_priority = null;
        $this->add_category_id = null;
        $this->add_plan_id = null;
        $this->resetValidation();
    }

    public function editTask(EditTaskAction $action): void
    {
        $this->edit_title = Str::squish($this->edit_title);

        try {
            $this->validate([
                'edit_title' => $this->rules()['edit_title'],
                'edit_description' => $this->rules()['edit_description'],
                'edit_date' => $this->rules()['edit_date'],
                'edit_estimated_minutes' => $this->rules()['edit_estimated_minutes'],
                'edit_alarm_days' => $this->rules()['edit_alarm_days'],
                'edit_priority' => $this->rules()['edit_priority'],
                'edit_category_id' => $this->rules()['edit_category_id'],
                'edit_plan_id' => $this->rules()['edit_plan_id'],
            ]);
        } catch (ValidationException $exception) {
            $this->dispatch('focus-first-error');

            throw $exception;
        }

        $task = $this->user->tasks()->findOrFail($this->editing_id);

        $result = $action->execute(
            $this->user,
            $task,
            $this->edit_title,
            $this->edit_description,
            $this->edit_date,
            $this->edit_estimated_minutes,
            TaskPriority::from($this->edit_priority),
            $this->edit_alarm_days,
            $this->edit_category_id,
            $this->edit_plan_id,
            request(),
        );

        $this->edit_error = match ($result) {
            EditTaskResult::RateLimited => 'rate_limited',
            EditTaskResult::InvalidCategory => 'invalid_category',
            EditTaskResult::InvalidPlan => 'invalid_plan',
            EditTaskResult::DateOutOfRange => 'date_out_of_range',
            EditTaskResult::Updated => null,
        };

        if ($this->edit_error) {
            return;
        }

        $this->edit_title = '';
        $this->edit_description = null;
        $this->edit_date = now()->format('Y-m-d');
        $this->edit_estimated_minutes = 0;
        $this->edit_alarm_days = 0;
        $this->edit_priority = 'medium';
        $this->edit_category_id = null;
        $this->edit_plan_id = null;
        $this->editing_id = null;
        unset($this->tasks);
        $this->dispatch('close-modal', id: 'edit-task-form');
        $this->dispatch('toast',
            title: __('Your task updated'),
            variant: 'info',
            duration: 3000,
            position: 'bottom-center'
        );
    }

    public function cancelEdit(): void
    {
        $this->edit_error = null;
        $this->editing_id = null;
        $this->resetValidation();
    }

    public function deleteTask(DeleteTaskAction $action): void
    {
        $task = $this->user->tasks()->findOrFail($this->deleting_id);

        $result = $action->execute($this->user, $task);

        if ($result === DeleteTaskResult::Deleted) {
            $this->deleting_id = null;
            unset($this->tasks);
            $this->dispatch('close-modal', id: 'delete-task-confirmation');
            $this->dispatch('toast',
                title: __('Your task deleted'),
                variant: 'info',
                duration: 3000,
                position: 'bottom-center'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->resetValidation();
    }

    public function completeTask(ToggleTaskDoneAction $action): void
    {
        $task = $this->user->tasks()->findOrFail($this->completing_id);

        $result = $action->execute($this->user, $task, request());

        $this->complete_error = match ($result) {
            ToggleTaskDoneResult::RateLimited => 'rate_limited',
            ToggleTaskDoneResult::Toggled => null,
            // Unreachable when completing (the cascade only fires on un-done) — kept for a total match.
            ToggleTaskDoneResult::Reactivated => null,
        };

        if ($this->complete_error) {
            return;
        }

        $this->completing_id = null;
        unset($this->tasks);
        $this->dispatch('close-modal', id: 'complete-task-confirmation');
        $this->dispatch('toast',
            title: __('Your task completed'),
            variant: 'info',
            duration: 3000,
            position: 'bottom-center'
        );
    }

    public function cancelComplete(): void
    {
        $this->complete_error = null;
        $this->completing_id = null;
        $this->resetValidation();
    }

    public function reopenTask(ToggleTaskDoneAction $action): void
    {
        $task = $this->user->tasks()->findOrFail($this->reopening_id);

        $result = $action->execute($this->user, $task, request());

        $this->reopen_error = match ($result) {
            ToggleTaskDoneResult::RateLimited => 'rate_limited',
            ToggleTaskDoneResult::Toggled => null,
            ToggleTaskDoneResult::Reactivated => null,
        };

        if ($this->reopen_error) {
            return;
        }

        $this->reopening_id = null;
        unset($this->tasks);
        $this->dispatch('close-modal', id: 'reopen-task-confirmation');
        $this->dispatch('toast',
            title: __('Your task re activated'),
            variant: 'info',
            duration: 3000,
            position: 'bottom-center'
        );

        // The un-done cascade may have reopened the plan — surface it (the R1 side effect used
        // to be silent here; the copy key already existed).
        if ($result === ToggleTaskDoneResult::Reactivated) {
            $this->dispatch('toast',
                title: __('Your plan re activated'),
                variant: 'info',
                duration: 3000,
                position: 'bottom-center'
            );
        }
    }

    public function cancelReopen(): void
    {
        $this->reopen_error = null;
        $this->reopening_id = null;
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'add_title' => ['required', 'string', 'max:255'],
            'add_description' => ['nullable', 'string', 'max:5000'],
            'add_date' => ['required', 'date_format:Y-m-d'],
            'add_estimated_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'add_alarm_days' => ['required', 'integer', 'min:0', 'max:365'],
            'add_priority' => ['required', Rule::enum(TaskPriority::class)],
            'add_category_id' => ['required', 'integer'],
            'add_plan_id' => ['nullable', 'integer'],
            'edit_title' => ['required', 'string', 'max:255'],
            'edit_description' => ['nullable', 'string', 'max:5000'],
            'edit_date' => ['required', 'date_format:Y-m-d'],
            'edit_estimated_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'edit_alarm_days' => ['required', 'integer', 'min:0', 'max:365'],
            'edit_priority' => ['required', Rule::enum(TaskPriority::class)],
            'edit_category_id' => ['required', 'integer'],
            'edit_plan_id' => ['nullable', 'integer'],
        ];
    }

    protected function messages(): array
    {
        return [
            'add_title.required' => __('Task title is required.'),
            'add_title.max' => __('Task title cannot exceed 255 characters.'),
            'add_description.max' => __('Task description cannot exceed 5000 characters.'),
            'add_date.required' => __('Task date is required.'),
            'add_date.date_format' => __('Task date must be in YYYY-MM-DD format.'),
            'add_estimated_minutes.required' => __('Estimated minutes is required.'),
            'add_estimated_minutes.min' => __('Estimated minutes must be at least 1.'),
            'add_estimated_minutes.max' => __('Estimated minutes cannot exceed 1440.'),
            'add_alarm_days.required' => __('Alarm days is required.'),
            'add_alarm_days.min' => __('Alarm days cannot be negative.'),
            'add_alarm_days.max' => __('Alarm days cannot exceed 365.'),
            'add_priority.required' => __('Priority is required.'),
            'add_priority.enum' => __('Selected priority is invalid.'),
            'add_category_id.required' => __('Category is required.'),
            'edit_title.required' => __('Task title is required.'),
            'edit_title.max' => __('Task title cannot exceed 255 characters.'),
            'edit_description.max' => __('Task description cannot exceed 5000 characters.'),
            'edit_date.required' => __('Task date is required.'),
            'edit_date.date_format' => __('Task date must be in YYYY-MM-DD format.'),
            'edit_estimated_minutes.required' => __('Estimated minutes is required.'),
            'edit_estimated_minutes.min' => __('Estimated minutes must be at least 1.'),
            'edit_estimated_minutes.max' => __('Estimated minutes cannot exceed 1440.'),
            'edit_alarm_days.required' => __('Alarm days is required.'),
            'edit_alarm_days.min' => __('Alarm days cannot be negative.'),
            'edit_alarm_days.max' => __('Alarm days cannot exceed 365.'),
            'edit_priority.required' => __('Priority is required.'),
            'edit_priority.enum' => __('Selected priority is invalid.'),
            'edit_category_id.required' => __('Category is required.'),
        ];
    }
};

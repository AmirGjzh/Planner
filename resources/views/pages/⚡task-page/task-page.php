<?php

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\EditTaskAction;
use App\Actions\Task\ToggleTaskDoneAction;
use App\Enums\CreateTaskResult;
use App\Enums\EditTaskResult;
use App\Enums\TaskPriority;
use App\Enums\ToggleTaskDoneResult;
use App\Models\Category;
use App\Models\Plan;
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

    public string $task_title = '';

    public ?string $task_description = null;

    public string $task_date = '';

    public int $task_estimated_minutes = 0;

    public int $task_alarm_days = 0;

    public string $task_priority = 'medium';

    public ?int $task_category_id = null;

    public ?int $task_plan_id = null;

    public ?int $editingTaskId = null;

    public string $editTitle = '';

    public ?string $editDescription = null;

    public string $editDate = '';

    public int $editEstimatedMinutes = 0;

    public int $editAlarmDays = 0;

    public string $editPriority = 'medium';

    public ?int $editCategoryId = null;

    public ?int $editPlanId = null;

    public DateRange $date_filter;

    public ?int $filterCategoryId = null;

    public ?int $filterPlanId = null;

    public ?string $filterStatus = null;

    public ?string $filterPriority = null;

    public ?bool $sortByDate = null;

    public ?bool $sortByPriority = null;

    public ?bool $sortByEstimatedMinutes = null;

    public function mount()
    {
        $this->task_date = now()->format('Y-m-d');
        $this->userId = auth()->id() ?? abort(403);
        $this->date_filter = DateRange::thisWeek();
    }

    #[Computed]
    public function user(): User
    {
        return User::query()->findOrFail($this->userId);
    }

    #[Computed]
    public function tasks()
    {
        $query = Task::query()
            ->with('category', 'plan')
            ->where('user_id', $this->userId);

        if ($this->date_filter->hasStart()) {
            $query->where('task_date', '>=', $this->date_filter->getStart());
        }

        if ($this->date_filter->hasEnd()) {
            $query->where('task_date', '<=', $this->date_filter->getEnd());
        }

        if ($this->filterCategoryId !== null) {
            $query->where('category_id', $this->filterCategoryId);
        }

        if ($this->filterPlanId !== null) {
            $query->where('plan_id', $this->filterPlanId);
        }

        if ($this->filterStatus === 'done') {
            $query->where('done', true);
        } elseif ($this->filterStatus === 'not_done') {
            $query->where('done', false);
        }

        if ($this->filterPriority !== null) {
            $query->where('priority', $this->filterPriority);
        }

        if ($this->sortByDate === true) {
            $query->orderBy('task_date');
        }

        if ($this->sortByPriority === true) {
            $query->orderBy('priority');
        }

        if ($this->sortByEstimatedMinutes === true) {
            $query->orderBy('estimated_minutes');
        }

        $hasCustomSort = $this->sortByDate === true
            || $this->sortByPriority === true
            || $this->sortByEstimatedMinutes === true;

        if (! $hasCustomSort) {
            $query->orderBy('task_date', 'desc')->orderBy('created_at', 'desc');
        }

        return $query->paginate(10);
    }

    #[Computed]
    public function workload(): ?array
    {
        $total = $this->tasks->sum('estimated_minutes');

        if ($total === 0) {
            return null;
        }

        return [
            'total_minutes' => $total,
            'hours' => intdiv($total, 60),
            'minutes' => $total % 60,
            'label' => match (true) {
                $total < 180 => 'Light',
                $total < 360 => 'Medium',
                default => 'Heavy',
            },
        ];
    }

    public function applyDateFilter(): void
    {
        unset($this->tasks, $this->workload);
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->where('user_id', $this->userId)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function plans()
    {
        return Plan::query()
            ->where('user_id', $this->userId)
            ->orderBy('name')
            ->get();
    }

    public function addTask(CreateTaskAction $action)
    {
        $this->validate([
            'task_title' => ['required', 'string', 'max:255'],
            'task_description' => ['nullable', 'string', 'max:5000'],
            'task_date' => 'required|date_format:Y-m-d',
            'task_estimated_minutes' => 'required|integer|min:1|max:1440',
            'task_alarm_days' => 'required|integer|min:0|max:365',
            'task_priority' => ['required', 'string', 'in:low,medium,high'],
            'task_category_id' => 'required|integer',
            'task_plan_id' => 'nullable|integer',
        ]);

        $result = $action->execute(
            $this->user,
            $this->task_title,
            $this->task_description,
            $this->task_date,
            $this->task_estimated_minutes,
            TaskPriority::from($this->task_priority),
            $this->task_alarm_days,
            $this->task_category_id,
            $this->task_plan_id,
            request(),
        );

        if ($result === CreateTaskResult::RateLimited) {
            $this->addError('task_form', 'Too many attempts. Please try again later.');

            return;
        }

        if ($result === CreateTaskResult::InvalidCategory) {
            $this->addError('task_category_id', 'The selected category is invalid.');

            return;
        }

        if ($result === CreateTaskResult::InvalidPlan) {
            $this->addError('task_plan_id', 'The selected plan is invalid.');

            return;
        }

        $this->reset('task_title', 'task_description', 'task_estimated_minutes', 'task_alarm_days');
        $this->task_date = now()->format('Y-m-d');
        $this->task_priority = 'medium';
        $this->task_category_id = null;
        $this->task_plan_id = null;
        unset($this->tasks, $this->workload);
    }

    public function deleteTask(int $taskId, DeleteTaskAction $action): void
    {
        $action->execute($this->user, $taskId);

        unset($this->tasks, $this->workload);
    }

    public function startEditing(int $taskId): void
    {
        $task = $this->user->tasks()->findOrFail($taskId);

        $this->editingTaskId = $taskId;
        $this->editTitle = $task->title;
        $this->editDescription = $task->description;
        $this->editDate = $task->task_date->format('Y-m-d');
        $this->editEstimatedMinutes = $task->estimated_minutes;
        $this->editAlarmDays = $task->day_before_alarm;
        $this->editPriority = $task->priority->value;
        $this->editCategoryId = $task->category_id;
        $this->editPlanId = $task->plan_id;
    }

    public function cancelEditing(): void
    {
        $this->reset('editingTaskId', 'editTitle', 'editDescription', 'editEstimatedMinutes', 'editAlarmDays');
        $this->editDate = now()->format('Y-m-d');
        $this->editPriority = 'medium';
        $this->editCategoryId = null;
        $this->editPlanId = null;
    }

    public function updateTask(EditTaskAction $action): void
    {
        $this->validate([
            'editTitle' => ['required', 'string', 'max:255'],
            'editDescription' => ['nullable', 'string', 'max:5000'],
            'editDate' => 'required|date_format:Y-m-d',
            'editEstimatedMinutes' => 'required|integer|min:1|max:1440',
            'editAlarmDays' => 'required|integer|min:0|max:365',
            'editPriority' => ['required', 'string', 'in:low,medium,high'],
            'editCategoryId' => 'required|integer',
            'editPlanId' => 'nullable|integer',
        ]);

        $result = $action->execute(
            $this->user,
            $this->editingTaskId,
            $this->editTitle,
            $this->editDescription,
            $this->editDate,
            $this->editEstimatedMinutes,
            TaskPriority::from($this->editPriority),
            $this->editAlarmDays,
            $this->editCategoryId,
            $this->editPlanId,
            request(),
        );

        if ($result === EditTaskResult::RateLimited) {
            $this->addError('edit_form', 'Too many attempts. Please try again later.');

            return;
        }

        if ($result === EditTaskResult::InvalidCategory) {
            $this->addError('editCategoryId', 'The selected category is invalid.');

            return;
        }

        if ($result === EditTaskResult::InvalidPlan) {
            $this->addError('editPlanId', 'The selected plan is invalid.');

            return;
        }

        $this->dispatch('close-modal', id: 'edit-task-modal');
        $this->cancelEditing();
        unset($this->tasks, $this->workload);
    }

    public function toggleTask(int $taskId, ToggleTaskDoneAction $action): void
    {
        $result = $action->execute($this->user, $taskId, request());

        if ($result === ToggleTaskDoneResult::RateLimited) {
            $this->addError('toggle_task', 'Too many attempts. Please try again later.');

            return;
        }

        unset($this->tasks, $this->workload);
    }
};

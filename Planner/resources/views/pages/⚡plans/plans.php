<?php

use App\Actions\Plan\CreatePlanAction;
use App\Actions\Plan\DeletePlanAction;
use App\Actions\Plan\EditPlanAction;
use App\Enums\CreatePlanResult;
use App\Enums\DeletePlanResult;
use App\Enums\EditPlanResult;
use App\Models\Plan;
use App\Models\User;
use App\View\Components\DateRange;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{

    public string $search = '';

    #[Locked]
    public int $userId;

    public string $plan_name = '';

    public ?string $description = null;

    public DateRange $range;

    public ?int $editingPlanId = null;

    public string $editName = '';

    public ?string $editDescription = null;

    public DateRange $editRange;

    public function mount()
    {
        $this->range = new DateRange(start: now(), end: now()->addDays(7));
        $this->editRange = new DateRange(start: now(), end: now()->addDays(7));
        $this->userId = auth()->id() ?? abort(403);
    }

    #[Computed]
    public function user(): User
    {
        return User::query()->findOrFail($this->userId);
    }

    #[Computed]
    public function plans()
    {
        return Plan::query()
            ->where('user_id', $this->userId)
            ->withCount('tasks')
            ->with('tasks')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function addPlan(CreatePlanAction $action)
    {
        $this->validate([
            'plan_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'range.start' => 'required|date_format:Y-m-d',
            'range.end' => 'required|date_format:Y-m-d|after:range.start',
        ]);

        $result = $action->execute(
            $this->user,
            $this->plan_name,
            $this->description,
            $this->range,
            request(),
        );

        if ($result === CreatePlanResult::RateLimited) {
            $this->addError('plan_form', 'Too many attempts. Please try again later.');

            return;
        }

        if ($result === CreatePlanResult::AlreadyExists) {
            $this->addError('plan_name', 'A plan with this name already exists.');

            return;
        }

        $this->reset('plan_name', 'description');
        $this->range = new DateRange(start: now(), end: now()->addDays(7));
        unset($this->plans);
    }

    public function deletePlan(int $planId, DeletePlanAction $action): void
    {
        $result = $action->execute($this->user, $planId);

        if ($result === DeletePlanResult::HasTasks) {
            $this->addError('plan_form', 'Cannot delete a plan that has tasks. Reassign or delete the tasks first.');

            return;
        }

        unset($this->plans);
    }

    public function startEditing(int $planId): void
    {
        $plan = $this->user->plans()->findOrFail($planId);

        $this->editingPlanId = $planId;
        $this->editName = $plan->name;
        $this->editDescription = $plan->description;
        $this->editRange = new DateRange(
            start: $plan->start_date,
            end: $plan->finish_date,
        );
    }

    public function cancelEditing(): void
    {
        $this->reset('editingPlanId', 'editName', 'editDescription');
        $this->editRange = new DateRange(start: now(), end: now()->addDays(7));
    }

    public function updatePlan(EditPlanAction $action): void
    {
        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editDescription' => ['nullable', 'string', 'max:5000'],
            'editRange.start' => 'required|date_format:Y-m-d',
            'editRange.end' => 'required|date_format:Y-m-d|after:editRange.start',
        ]);

        $result = $action->execute(
            $this->user,
            $this->editingPlanId,
            $this->editName,
            $this->editDescription,
            $this->editRange,
            request(),
        );

        if ($result === EditPlanResult::RateLimited) {
            $this->addError('edit_form', 'Too many attempts. Please try again later.');

            return;
        }

        if ($result === EditPlanResult::AlreadyExists) {
            $this->addError('editName', 'A plan with this name already exists.');

            return;
        }

        $this->dispatch('close-modal', id: 'edit-plan-modal');
        $this->cancelEditing();
        unset($this->plans);
    }
};

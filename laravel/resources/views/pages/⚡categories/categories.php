<?php

use App\Actions\Category\CreateCategoryAction;
use App\Actions\Category\DeleteCategoryAction;
use App\Actions\Category\EditCategoryAction;
use App\Enums\CreateCategoryResult;
use App\Enums\DeleteCategoryResult;
use App\Enums\EditCategoryResult;
use App\Livewire\Concerns\HasUser;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use HasUser, WithPagination;

    public string $add_category = '';

    public ?string $add_error = null;

    public ?int $editing_id = null;

    public string $edit_name = '';

    public ?string $edit_error = null;

    public ?int $deleting_id = null;

    public ?string $delete_error = null;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'latest';

    public function mount(): void
    {
        if ($toast = session()->pull('toast')) {
            $this->dispatch('toast', ...$toast);
        }
    }

    #[Computed]
    public function categories()
    {
        $search = trim($this->search);

        return Category::query()
            ->select(['id', 'name', 'user_id', 'created_at'])
            ->where('user_id', auth()->id())
            ->when($search, fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', [Str::lower('%'.$search.'%')]))
            ->withCount('tasks')
            ->when($this->sort === 'name', fn ($q) => $q->orderBy('name'))
            ->when($this->sort === 'tasks', fn ($q) => $q->orderByDesc('tasks_count')->orderBy('name'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest()->orderBy('id'))
            ->paginate(6)->onEachSide(1);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function addCategory(CreateCategoryAction $action): void
    {
        $this->add_category = preg_replace('/^[\s\x{200C}]+|[\s\x{200C}]+$/u', '', trim($this->add_category));

        try {
            $this->validate([
                'add_category' => $this->rules()['add_category'],
            ]);
        } catch (ValidationException $exception) {
            $this->dispatch('focus-first-error');

            throw $exception;
        }

        $result = $action->execute($this->user, $this->add_category, request());

        $this->add_error = match ($result) {
            CreateCategoryResult::AlreadyExists => 'already_exists',
            CreateCategoryResult::RateLimited => 'rate_limited',
            CreateCategoryResult::Created => null,
        };

        if ($this->add_error) {
            return;
        }

        $this->add_category = '';
        unset($this->categories);
        $this->dispatch('close-modal',
            id: 'add-category-form'
        );
        $this->dispatch('toast',
            title: __('Your category created'),
            variant: 'success',
            duration: 3000,
            position: 'bottom-center'
        );
    }

    public function cancelAdd(): void
    {
        $this->add_error = null;
        $this->add_category = '';
        $this->resetValidation();
    }

    public function editCategory(EditCategoryAction $action): void
    {
        $this->edit_name = preg_replace('/^[\s\x{200C}]+|[\s\x{200C}]+$/u', '', trim($this->edit_name));

        try {
            $this->validate([
                'edit_name' => $this->rules()['edit_name'],
            ]);
        } catch (ValidationException $exception) {
            $this->dispatch('focus-first-error');

            throw $exception;
        }

        $category = $this->user->categories()->findOrFail($this->editing_id);
        $result = $action->execute($this->user, $category, $this->edit_name, request());

        $this->edit_error = match ($result) {
            EditCategoryResult::AlreadyExists => 'already_exists',
            EditCategoryResult::RateLimited => 'rate_limited',
            EditCategoryResult::Updated => null,
        };

        if ($this->edit_error) {
            return;
        }

        $this->edit_name = '';
        $this->editing_id = null;
        unset($this->categories);
        $this->dispatch('close-modal',
            id: 'edit-category-form'
        );
        $this->dispatch('toast',
            title: __('Your category updated'),
            variant: 'info',
            duration: 3000,
            position: 'bottom-center'
        );
    }

    public function cancelEdit(): void
    {
        $this->edit_error = null;
        $this->edit_name = '';
        $this->editing_id = null;
        $this->resetValidation();
    }

    public function deleteCategory(DeleteCategoryAction $action): void
    {
        $category = $this->user->categories()->findOrFail($this->deleting_id);

        $result = $action->execute($this->user, $category);

        $this->delete_error = match ($result) {
            DeleteCategoryResult::HasTasks => 'has_tasks',
            DeleteCategoryResult::Deleted => null,
        };

        if ($result === DeleteCategoryResult::Deleted) {
            $this->deleting_id = null;
            unset($this->categories);
            $this->dispatch('close-modal', id: 'delete-category-confirmation');
            $this->dispatch('toast',
                title: __('Your category deleted'),
                variant: 'info',
                duration: 3000,
                position: 'bottom-center'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->delete_error = null;
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'add_category' => ['required', 'string', 'max:255'],
            'edit_name' => ['required', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'add_category.required' => __('Category name is required.'),
            'add_category.max' => __('Category name cannot exceed 255 characters.'),
            'edit_name.required' => __('Category name is required.'),
            'edit_name.max' => __('Category name cannot exceed 255 characters.'),
        ];
    }
};

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
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use HasUser, WithPagination;

    public string $add_category = '';

    public ?string $add_error = null;

    public ?string $add_success = null;

    public ?int $editing_id = null;

    public string $edit_name = '';

    public ?string $edit_error = null;

    public ?string $edit_success = null;

    public ?int $deleting_id = null;

    public ?string $delete_error = null;

    #[Url]
    public string $search = '';

    public string $sort = 'latest';

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->where('user_id', auth()->id())
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->withCount('tasks')
            ->when($this->sort === 'name', fn ($q) => $q->orderBy('name'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest())
            ->paginate(6)->onEachSide(1);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function addCategory(CreateCategoryAction $action): void
    {
        $this->validate([
            'add_category' => $this->rules()['add_category'],
        ]);

        $name = Str::ucfirst(Str::lower($this->add_category));

        $result = $action->execute($this->user, $name, request());

        $this->add_error = match ($result) {
            CreateCategoryResult::AlreadyExists => 'already_exists',
            CreateCategoryResult::RateLimited => 'rate_limited',
            CreateCategoryResult::Created => null,
        };

        if ($this->add_error) {
            $this->add_success = null;

            return;
        }

        $this->add_category = '';
        $this->add_success = 'created';
        unset($this->categories);
    }

    public function cancelAdd(): void
    {
        $this->add_error = null;
        $this->add_success = null;
        $this->add_category = '';
        $this->resetValidation();
    }

    public function editCategory(EditCategoryAction $action): void
    {
        $this->validate([
            'edit_name' => $this->rules()['edit_name'],
        ]);

        $name = Str::ucfirst(Str::lower($this->edit_name));

        $category = $this->user->categories()->findOrFail($this->editing_id);
        $result = $action->execute($this->user, $category, $name, request());

        $this->edit_error = match ($result) {
            EditCategoryResult::AlreadyExists => 'already_exists',
            EditCategoryResult::RateLimited => 'rate_limited',
            EditCategoryResult::Updated => null,
        };

        if ($this->edit_error) {
            $this->edit_success = null;

            return;
        }

        $this->edit_name = '';
        $this->editing_id = null;
        $this->edit_success = 'updated';
        unset($this->categories);
    }

    public function cancelEdit(): void
    {
        $this->edit_error = null;
        $this->edit_success = null;
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
            'add_category.required' => 'Category name is required.',
            'add_category.max' => 'Category name must not exceed 255 characters.',
            'edit_name.required' => 'Category name is required.',
            'edit_name.max' => 'Category name must not exceed 255 characters.',
        ];
    }
};

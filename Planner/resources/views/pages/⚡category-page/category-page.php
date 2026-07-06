<?php

use App\Actions\Category\CreateCategoryAction;
use App\Actions\Category\DeleteCategoryAction;
use App\Actions\Category\EditCategoryAction;
use App\Enums\CreateCategoryResult;
use App\Enums\DeleteCategoryResult;
use App\Enums\EditCategoryResult;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $userId;

    public function mount(): void
    {
        $this->userId = auth()->id() ?? abort(403);
    }

    #[Computed]
    public function user(): User
    {
        return User::query()->findOrFail($this->userId);
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->where('user_id', $this->userId)
            ->withCount('tasks')
            ->paginate(10);
    }

    public function addCategory(string $name, CreateCategoryAction $action): void
    {
        $validator = Validator::make(
            ['name' => $name],
            ['name' => ['required', 'string', 'max:255']]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->addError('new_category', $error);
            }

            return;
        }

        $name = Str::ucfirst(Str::lower($name));

        $result = $action->execute($this->user, $name, request());

        if ($result === CreateCategoryResult::AlreadyExists) {
            $this->addError('new_category', 'This category already exists.');

            return;
        }

        if ($result === CreateCategoryResult::RateLimited) {
            $this->addError('new_category', 'Too many attempts. Please try again later.');

            return;
        }

        unset($this->categories);
    }

    public function editCategory(int $categoryId, string $name, EditCategoryAction $action): void
    {
        $validator = Validator::make(
            ['name' => $name],
            ['name' => ['required', 'string', 'max:255']]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->addError('edit_category', $error);
            }

            return;
        }

        $name = Str::ucfirst(Str::lower($name));

        $category = $this->user->categories()->findOrFail($categoryId);
        $result = $action->execute($this->user, $category, $name, request());

        if ($result === EditCategoryResult::AlreadyExists) {
            $this->addError('edit_category', 'This category already exists.');

            return;
        }

        if ($result === EditCategoryResult::RateLimited) {
            $this->addError('edit_category', 'Too many attempts. Please try again later.');

            return;
        }

        unset($this->categories);
    }

    public function deleteCategory(int $categoryId, DeleteCategoryAction $action): void
    {
        $category = $this->user->categories()->findOrFail($categoryId);

        $result = $action->execute($this->user, $category);

        if ($result === DeleteCategoryResult::HasTasks) {
            $this->addError('delete_category', 'Cannot delete a category that has tasks. Reassign or delete the tasks first.');

            return;
        }

        unset($this->categories);
    }
};

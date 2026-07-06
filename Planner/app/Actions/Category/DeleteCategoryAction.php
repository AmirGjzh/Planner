<?php

namespace App\Actions\Category;

use App\Enums\DeleteCategoryResult;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DeleteCategoryAction
{
    public function execute(User $user, Category $category): DeleteCategoryResult
    {
        abort_unless($user->can('delete', $category), 403);

        if ($category->tasks()->exists()) {
            Log::warning('Category deletion failed, has tasks assigned.', [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => $category->name,
                'tasks_count' => $category->tasks()->count(),
            ]);

            return DeleteCategoryResult::HasTasks;
        }

        $category->delete();

        Log::info('Category deleted.', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => $category->name,
        ]);

        return DeleteCategoryResult::Deleted;
    }
}

<?php

namespace App\Actions\Category;

use App\Enums\DeleteCategoryResult;
use App\Models\Category;
use App\Models\User;
use Psr\Log\LoggerInterface;

final class DeleteCategoryAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(User $user, Category $category): DeleteCategoryResult
    {
        abort_unless($user->can('delete', $category), 403);
        $tasks_count = $category->tasks()->count();

        if ($tasks_count > 0) {
            $this->logger->warning('Category deletion failed, has tasks assigned.', [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => $category->name,
                'tasks_count' => $tasks_count,
            ]);

            return DeleteCategoryResult::HasTasks;
        }

        $category->delete();

        $this->logger->info('Category deleted.', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => $category->name,
        ]);

        return DeleteCategoryResult::Deleted;
    }
}

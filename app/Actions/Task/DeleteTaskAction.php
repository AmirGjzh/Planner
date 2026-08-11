<?php

namespace App\Actions\Task;

use App\Enums\DeleteTaskResult;
use App\Models\Task;
use App\Models\User;
use Psr\Log\LoggerInterface;

final class DeleteTaskAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(User $user, Task $task): DeleteTaskResult
    {
        abort_unless($user->can('delete', $task), 403);

        $task->delete();

        $this->logger->info('Task deleted.', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'title' => $task->title,
        ]);

        return DeleteTaskResult::Deleted;
    }
}

<?php

namespace App\Actions\Task;

use App\Enums\DeleteTaskResult;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DeleteTaskAction
{
    public function execute(User $user, int $taskId): DeleteTaskResult
    {
        $task = $user->tasks()->findOrFail($taskId);

        abort_unless($user->can('delete', $task), 403);

        $task->delete();

        Log::info('Task deleted.', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'title' => $task->title,
        ]);

        return DeleteTaskResult::Deleted;
    }
}

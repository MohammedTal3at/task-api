<?php

namespace App\Services\Task;

use App\Enums\TaskStatus;
use App\Events\TaskStatusToggled;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class ToggleTaskStatusService
{
    public function __construct(private TaskRepositoryInterface $repository)
    {
    }

    public function execute(int $taskId, int $userId): Task
    {
        $task = $this->repository->findById($taskId);

        if (!$task) {
            throw new NotFoundHttpException('Task not found.');
        }

        $oldStatus = $task->status;

        $nextStatus = match ($oldStatus) {
            TaskStatus::PENDING => TaskStatus::IN_PROGRESS,
            TaskStatus::IN_PROGRESS => TaskStatus::COMPLETED,
            TaskStatus::COMPLETED => TaskStatus::PENDING,
        };

        $updatedTask = $this->repository->updateStatus($taskId, $nextStatus, $task->version);

        TaskStatusToggled::dispatch(
            $taskId,
            $userId,
            Carbon::now(),
            ['status' => $oldStatus],
            ['status' => $nextStatus]
        );

        return $updatedTask;
    }
}

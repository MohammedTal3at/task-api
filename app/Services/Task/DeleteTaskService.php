<?php

namespace App\Services\Task;

use App\Events\TaskDeleted;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class DeleteTaskService
{
    public function __construct(private TaskRepositoryInterface $repository)
    {
    }

    public function execute(int $taskId, int $userId): void
    {
        $task = $this->repository->findById($taskId);

        if (empty($task)) {
            throw new NotFoundHttpException('Task not found.');
        }

        $task->delete();

        TaskDeleted::dispatch(
            $taskId,
            $userId,
            Carbon::now(),
            $task->getAttributes()
        );
    }
}

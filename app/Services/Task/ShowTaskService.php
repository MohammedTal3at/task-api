<?php

namespace App\Services\Task;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class ShowTaskService
{
    public function __construct(private TaskRepositoryInterface $repository)
    {
    }

    public function execute(int $taskId): Task
    {
        $task = $this->repository->findById($taskId);

        if (!$task) {
            throw new NotFoundHttpException('Task not found.');
        }

        return $task;
    }
}

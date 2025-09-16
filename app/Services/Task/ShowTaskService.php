<?php

namespace App\Services\Task;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\Auth\Traits\AuthorizationTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class ShowTaskService
{
    use AuthorizationTrait;

    public function __construct(private TaskRepositoryInterface $repository)
    {
    }

    public function execute(int $taskId, User $user): Task
    {
        $this->canUserManageTask($this->repository, $user, $taskId);

        $task = $this->repository->findById($taskId);

        if (!$task) {
            throw new NotFoundHttpException('Task not found.');
        }

        return $task;
    }
}

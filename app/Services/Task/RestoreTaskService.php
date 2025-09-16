<?php

namespace App\Services\Task;

use App\Events\TaskRestored;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\Auth\Traits\AuthorizationTrait;
use Illuminate\Support\Carbon;

readonly class RestoreTaskService
{
    use AuthorizationTrait;

    public function __construct(private TaskRepositoryInterface $repository)
    {
    }

    public function execute(int $taskId, int $userId): Task
    {
        $restoredTask = $this->repository->restore($taskId);

        TaskRestored::dispatch(
            $taskId,
            $userId,
            Carbon::now(),
            $restoredTask->getAttributes()
        );

        return $restoredTask;
    }
}

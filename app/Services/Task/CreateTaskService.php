<?php

namespace App\Services\Task;

use App\Dtos\CreateTaskDto;
use App\Events\TaskCreated;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateTaskService
{
    public function __construct(
        protected TaskRepositoryInterface $repository
    )
    {
    }

    public function execute(CreateTaskDto $dto): Task
    {
        $task = $this->repository->create($dto);

        TaskCreated::dispatch(
            $task->id,
            $dto->creatorId,
            now(),
            [],
            $task->getAttributes()
        );

        return $task;
    }
}

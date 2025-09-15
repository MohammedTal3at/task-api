<?php

namespace App\Services\Task;

use App\Dtos\CreateTaskDto;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;

class CreateTaskService
{
    public function __construct(
        protected TaskRepositoryInterface $repository
    )
    {
    }

    public function execute(CreateTaskDto $dto): Task
    {
        return $this->repository->create($dto);
    }
}

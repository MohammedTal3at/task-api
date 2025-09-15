<?php

namespace App\Services\TaskLog;

use App\Dtos\CreateTaskLogDto;
use App\Repositories\Contracts\TaskLogRepositoryInterface;

readonly class CreateTaskLogService
{
    public function __construct(private TaskLogRepositoryInterface $repository)
    {
    }

    public function execute(CreateTaskLogDto $dto): void
    {
        $this->repository->create($dto);
    }
}

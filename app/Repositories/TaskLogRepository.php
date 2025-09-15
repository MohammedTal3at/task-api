<?php

namespace App\Repositories;

use App\Dtos\CreateTaskLogDto;
use App\Models\TaskLog;
use App\Repositories\Contracts\TaskLogRepositoryInterface;

class TaskLogRepository implements TaskLogRepositoryInterface
{
    public function create(CreateTaskLogDto $dto): void
    {
        TaskLog::create([
            'task_id' => $dto->taskId,
            'user_id' => $dto->userId,
            'operation_type' => $dto->operationType,
            'changes' => $dto->changes,
        ]);
    }
}

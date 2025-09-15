<?php

namespace App\Dtos;

use App\Enums\TaskLogOperationType;

readonly class CreateTaskLogDto
{
    public function __construct(
        public int $taskId,
        public int $userId,
        public TaskLogOperationType $operationType,
        public array $changes = []
    ) {
    }
}

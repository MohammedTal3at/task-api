<?php

namespace App\Repositories\Contracts;

use App\Dtos\CreateTaskDto;
use App\Dtos\ListTasksDto;
use App\Dtos\UpdateTaskDto;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function create(CreateTaskDto $createTaskDto): ?Task;

    public function findById(int $taskId): ?Task;

    public function getPaginated(ListTasksDto $dto): LengthAwarePaginator|CursorPaginator;

    public function update(int $taskId, UpdateTaskDto $dto, int $expectedVersion): Task;

    public function restore(int $taskId): Task;

    public function updateStatus(int $taskId, TaskStatus $newStatus, int $expectedVersion): Task;
}

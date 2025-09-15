<?php

namespace App\Repositories;

use App\Dtos\CreateTaskDto;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{

    public function create(CreateTaskDto $dto): ?Task
    {
        $task = Task::create([
            'title' => $dto->title,
            'description' => $dto->description,
            'status' => $dto->status,
            'priority' => $dto->priority,
            'due_date' => $dto->due_date,
            'assigned_to' => $dto->assigned_to,
            'metadata' => $dto->metadata,
        ]);

        if (!empty($dto->tags)) {
            $task->tags()->sync($dto->tags);
            $task->load(['tags:id,name', 'assignee:id,name']);
        }

        return $task;
    }
}

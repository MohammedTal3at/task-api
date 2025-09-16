<?php

namespace App\Repositories;

use App\Dtos\CreateTaskDto;
use App\Dtos\ListTasksDto;
use App\Dtos\UpdateTaskDto;
use App\Exceptions\ConcurrencyException;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            'metadata' => $dto->metadata
        ]);

        if (!empty($dto->tags)) {
            $task->tags()->sync($dto->tags);
            $task->load(['tags:id,name', 'assignee:id,name']);
        }

        return $task;
    }

    public function findById(int $taskId): ?Task
    {
        return Task::with(['tags', 'assignee'])->find($taskId);
    }

    public function getPaginated(ListTasksDto $dto): LengthAwarePaginator|CursorPaginator
    {
        $query = Task::with(['tags:id,name,color', 'assignee:id,name']);

        $query = $this->applyFilters($query, $dto);
        $query = $this->applySorting($query, $dto);

        if ($dto->cursor) {
            return $query->cursorPaginate(perPage: $dto->perPage, cursor: $dto->cursor);
        } else {
            return $query->paginate(perPage: $dto->perPage, page: $dto->cursor);
        }
    }

    private function applyFilters(Builder $query, ListTasksDto $dto): Builder
    {
        if (!empty($dto->status)) {
            $query->whereIn('status', $dto->status);
        }
        if (!empty($dto->priority)) {
            $query->whereIn('priority', $dto->priority);
        }
        if (!empty($dto->assignedTo)) {
            $query->whereIn('assigned_to', $dto->assignedTo);
        }
        if ($dto->dueDateStart) {
            $query->where('due_date', '>=', $dto->dueDateStart);
        }
        if ($dto->dueDateEnd) {
            $query->where('due_date', '<=', $dto->dueDateEnd);
        }
        if (!empty($dto->tags)) {
            $query->whereHas('tags', function ($q) use ($dto) {
                $q->whereIn('tags.id', $dto->tags);
            });
        }
        if ($dto->keyword) {
            $keyword = $dto->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->whereFullText(['title', 'description'], $keyword);
            });
        }

        return $query;
    }

    private function applySorting(Builder $query, ListTasksDto $dto): Builder
    {
        $sortBy = $dto->sortBy ?? 'created_at';
        $sortOrder = $dto->sortOrder ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }

    /**
     * @throws ConcurrencyException
     * @throws ModelNotFoundException
     */
    public function update(int $taskId, UpdateTaskDto $dto, int $expectedVersion): Task
    {
        $data = $dto->toBeUpdated();

        // Add version increment to the update data
        $data['version'] = $expectedVersion + 1;
        $data['updated_at'] = Carbon::now();

        // Update with optimistic lock check
        $updatedCount = Task::where('id', $taskId)
            ->where('version', $expectedVersion)
            ->update($data);

        // Handle proper error exception
        if ($updatedCount === 0) {
            if (!Task::where('id', $taskId)->exists()) {
                throw new ModelNotFoundException("Task not found with ID: {$taskId}");
            }
            throw new ConcurrencyException("Task with ID: {$taskId} has been modified by another user.");
        }

        return Task::with(['tags:id,name', 'assignee:id,name'])->findOrFail($taskId);
    }

    public function synTags(Task $task, array $tagIds): Task
    {
        $task->tags()->sync($tagIds);
        return $task->load(['tags:id,name', 'assignee:id,name']);
    }
}

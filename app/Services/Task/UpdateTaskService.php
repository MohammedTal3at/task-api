<?php

namespace App\Services\Task;

use App\Dtos\UpdateTaskDto;
use App\Enums\TaskStatus;
use App\Events\TaskUpdated;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;

readonly class UpdateTaskService
{
    public function __construct(private TaskRepositoryInterface $repository)
    {
    }

    public function execute(int $taskId, UpdateTaskDto $dto, int $userId): Task
    {
        return DB::transaction(function () use ($taskId, $dto, $userId) {

            $task = $this->repository->findById($taskId);

            if (!$task) {
                throw new NotFoundHttpException('Task not found.');
            }

            if ($dto->due_date !== null) {
                $this->validateDueDate($task, $dto);
            }

            $oldAttributes = $task->getAttributes();

            $updatedTask = $this->repository->update($taskId, $dto, $task->version);

            if ($dto->tags !== null) {
                $this->repository->synTags($updatedTask, $dto->tags);
            }

            TaskUpdated::dispatch(
                $taskId,
                $userId,
                Carbon::now(),
                $oldAttributes,
                $updatedTask->getAttributes()
            );

            return $updatedTask;
        });
    }

    /**
     * Validate that the due date shouldn't be in the past for pending or in-progress, except the status will be updated to completed as well
     *
     * @throws ValidationException
     */
    private function validateDueDate(Task $task, UpdateTaskDto $dto): void
    {
        $dueDate = Carbon::parse($dto->due_date);
        if ($dueDate->isPast() && in_array($task->status, [TaskStatus::PENDING, TaskStatus::IN_PROGRESS]) && $dto->status != TaskStatus::COMPLETED)
        {
            throw ValidationException::withMessages([
                'due_date' => 'The due date cannot be in the past for tasks with pending or in-progress status.'
            ]);

        }
    }
}

<?php

namespace App\Listeners;

use App\Dtos\CreateTaskLogDto;
use App\Enums\TaskLogOperationType;
use App\Events\TaskCreated;
use App\Services\TaskLog\CreateTaskLogService;
use Illuminate\Support\Facades\Log;

// Ideally it should be queued, but just for demonstration.
class TaskLogListener
{
    private CreateTaskLogService $createTaskLogService;

    /**
     * Create the event listener.
     */
    public function __construct(CreateTaskLogService $createTaskLogService)
    {
        $this->createTaskLogService = $createTaskLogService;
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        $operationType = $this->getOperationType($event);


        if ($operationType) {
            $dto = new CreateTaskLogDto(
                taskId: $event->taskId,
                userId: $event->userId,
                operationType: $operationType,
                changes: [
                    'old' => $event->oldAttributes,
                    'new' => $event->newAttributes,
                ]
            );

            $this->createTaskLogService->execute($dto);
        } else {
            Log::alert('Unhandled task log operation type.', $event);
        }
    }

    private function getOperationType(TaskCreated $event): ?TaskLogOperationType
    {
        $eventClass = get_class($event);

        return match ($eventClass) {
            TaskCreated::class => TaskLogOperationType::CREATED,
            default => null,
        };
    }
}

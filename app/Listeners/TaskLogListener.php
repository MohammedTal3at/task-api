<?php

namespace App\Listeners;

use App\Dtos\CreateTaskLogDto;
use App\Enums\TaskLogOperationType;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskRestored;
use App\Events\TaskUpdated;
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
    public function handle(TaskCreated|TaskDeleted|TaskUpdated|TaskRestored $event): void
    {
        $operationType = $this->getOperationType($event);

        if ($operationType) {
            $dto = new CreateTaskLogDto(
                taskId: $event->taskId,
                userId: $event->userId,
                operationType: $operationType,
                changes: [
                    'old' => property_exists($event, 'oldAttributes') ? $event->oldAttributes : [],
                    'new' => property_exists($event, 'newAttributes') ? $event->newAttributes : [],
                ]
            );

            $this->createTaskLogService->execute($dto);
        } else {
            Log::alert('Unhandled task log operation type.', (array) $event);
        }
    }

    private function getOperationType(TaskCreated|TaskDeleted|TaskUpdated|TaskRestored $event): ?TaskLogOperationType
    {
        return match (get_class($event)) {
            TaskCreated::class => TaskLogOperationType::CREATED,
            TaskDeleted::class => TaskLogOperationType::DELETED,
            TaskUpdated::class => TaskLogOperationType::UPDATED,
            TaskRestored::class => TaskLogOperationType::RESTORED,
            default => null,
        };
    }
}

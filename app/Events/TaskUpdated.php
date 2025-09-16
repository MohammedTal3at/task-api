<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class TaskUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $taskId;
    public int $userId;
    public Carbon $eventDate;
    public array $oldAttributes;
    public array $newAttributes;

    /**
     * Create a new event instance.
     */
    public function __construct(int $taskId, int $userId, Carbon $eventDate, array $oldAttributes, array $newAttributes)
    {
        $this->taskId = $taskId;
        $this->userId = $userId;
        $this->eventDate = $eventDate;
        $this->oldAttributes = $oldAttributes;
        $this->newAttributes = $newAttributes;
    }
}

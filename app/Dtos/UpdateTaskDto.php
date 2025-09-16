<?php

namespace App\Dtos;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\Task\UpdateTaskRequest;

readonly class UpdateTaskDto
{
    public function __construct(
        public ?string       $title = null,
        public ?string       $description = null,
        public ?TaskStatus   $status = null,
        public ?TaskPriority $priority = null,
        public ?string       $due_date = null,
        public ?int          $assigned_to = null,
        public ?array        $metadata = null,
        public ?array        $tags = null,
    )
    {
    }

    public static function createFromRequest(UpdateTaskRequest $request): self
    {
        $data = $request->validated();

        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            status: isset($data['status']) ? TaskStatus::from($data['status']) : null,
            priority: isset($data['priority']) ? TaskPriority::from($data['priority']) : null,
            due_date: $data['due_date'] ?? null,
            assigned_to: $data['assigned_to'] ?? null,
            metadata: $data['metadata'] ?? null,
            tags: $data['tags'] ?? null,
        );
    }

    public function toBeUpdated(): array
    {
        return array_filter(
            get_object_vars($this),
            function ($value, $name) {
                return !is_null($value) && $name !== 'tags';
            },
            ARRAY_FILTER_USE_BOTH
        );
    }
}

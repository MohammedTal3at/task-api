<?php

namespace App\Dtos;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\Task\CreateTaskRequest;

readonly class CreateTaskDto
{
    public function __construct(
        public string  $title,
        public ?string $description,
        public string  $status,
        public string  $priority,
        public ?string $due_date,
        public ?int    $assigned_to,
        public ?array  $metadata,
        public array   $tags,
        public int     $creatorId
    )
    {
    }

    public static function createFromRequest(CreateTaskRequest $request): self
    {
        $data = $request->validated();

        return new self(
            $data['title'],
            $data['description'] ?? null,
            $data['status'] ?? TaskStatus::PENDING->value,
            $data['priority'] ?? TaskPriority::MEDIUM->value,
            $data['due_date'] ?? null,
            $data['assigned_to'] ?? null,
            $data['metadata'] ?? null,
            $data['tags'] ?? [],
            creatorId: $request->user()->id,
        );
    }
}

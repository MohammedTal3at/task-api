<?php

namespace App\Dtos;

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
    ) {}

    public static function createFromRequest(CreateTaskRequest $request): self
    {
        $data = $request->validated();

        return new self(
            $data['title'],
            $data['description'] ?? null,
            $data['status'] ?? 'pending',
            $data['priority'] ?? 'medium',
            $data['due_date'] ?? null,
            $data['assigned_to'] ?? null,
            $data['metadata'] ?? null,
            $data['tags'] ?? [],
        );
    }
}

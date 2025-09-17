<?php

namespace App\Dtos;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Http\Requests\Task\ListTasksRequest;
use App\Models\User;

readonly class ListTasksDto
{
    public function __construct(
        public ?array  $status = null,
        public ?array  $priority = null,
        public ?array  $assignedTo = null,
        public ?string $dueDateStart = null,
        public ?string $dueDateEnd = null,
        public ?array  $tags = null,
        public ?string $keyword = null,
        public ?string $sortBy = null,
        public ?string $sortOrder = null,
        public int     $perPage = 15,
        public ?int    $page = null,
        public ?string $cursor = null,
    )
    {
    }

    public static function createFromRequest(ListTasksRequest $request): self
    {
        $data = $request->validated();
        /**
         * @var User $user
         */
        $user = $request->user();

        return new self(
            status: $data['status'] ?? null,
            priority: $data['priority'] ?? null,
            assignedTo: !$user->isAdmin() ? [$user->id] : ($data['assigned_to'] ?? null),
            dueDateStart: $data['due_date_start'] ?? null,
            dueDateEnd: $data['due_date_end'] ?? null,
            tags: $data['tags'] ?? null,
            keyword: $data['keyword'] ?? null,
            sortBy: $data['sort_by'] ?? null,
            sortOrder: $data['sort_order'] ?? null,
            perPage: !empty($data['per_page']) ? min($data['per_page'], 15) : 15,
            page: $data['page'] ?? null,
            cursor: $data['cursor'] ?? null,
        );
    }
}

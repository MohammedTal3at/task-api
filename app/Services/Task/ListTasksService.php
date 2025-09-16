<?php

namespace App\Services\Task;

use App\Dtos\ListTasksDto;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class ListTasksService
{
    public function __construct(private TaskRepositoryInterface $repository)
    {
    }

    public function execute(ListTasksDto $dto): LengthAwarePaginator|CursorPaginator
    {
        return $this->repository->getPaginated($dto);
    }
}

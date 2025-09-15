<?php

namespace App\Repositories\Contracts;

use App\Dtos\CreateTaskLogDto;

interface TaskLogRepositoryInterface
{
    public function create(CreateTaskLogDto $dto): void;
}

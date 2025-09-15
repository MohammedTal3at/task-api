<?php

namespace App\Repositories\Contracts;

use App\Dtos\CreateTaskDto;
use App\Models\Task;

interface TaskRepositoryInterface
{
    public function create(CreateTaskDto $createTaskDto): ?Task;
}

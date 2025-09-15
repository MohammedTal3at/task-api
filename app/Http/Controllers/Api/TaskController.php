<?php

namespace App\Http\Controllers\Api;

use App\Dtos\CreateTaskDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\Task\CreateTaskService;

class TaskController extends Controller
{
    public function __construct(
        protected CreateTaskService $service
    )
    {
    }

    public function create(CreateTaskRequest $request): TaskResource
    {
        $dto = CreateTaskDto::createFromRequest($request);
        return new TaskResource($this->service->execute($dto));
    }
}

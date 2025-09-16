<?php

namespace App\Http\Controllers\Api;

use App\Dtos\CreateTaskDto;
use App\Dtos\ListTasksDto;
use App\Dtos\UpdateTaskDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\ListTasksRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\Task\CreateTaskService;
use App\Services\Task\DeleteTaskService;
use App\Services\Task\ListTasksService;
use App\Services\Task\RestoreTaskService;
use App\Services\Task\ShowTaskService;
use App\Services\Task\ToggleTaskStatusService;
use App\Services\Task\UpdateTaskService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(ListTasksRequest $request, ListTasksService $service): AnonymousResourceCollection
    {
        $dto = ListTasksDto::createFromRequest($request);
        return TaskResource::collection($service->execute($dto));
    }

    public function show(int $taskId, Request $request, ShowTaskService $service): TaskResource
    {
        $task = $service->execute($taskId, $request->user());
        return new TaskResource($task);
    }

    public function create(CreateTaskRequest $request, CreateTaskService $service): TaskResource
    {
        $dto = CreateTaskDto::createFromRequest($request);
        return new TaskResource($service->execute($dto));
    }

    public function update(int $taskId, UpdateTaskRequest $request, UpdateTaskService $service): TaskResource
    {
        $dto = UpdateTaskDto::createFromRequest($request);
        return new TaskResource($service->execute($taskId, $dto, $request->user()->id));
    }

    public function destroy(int $taskId, Request $request, DeleteTaskService $service): Response
    {
        $service->execute($taskId, $request->user()->id);

        return response()->noContent();
    }

    public function restore(int $taskId, Request $request, RestoreTaskService $service): TaskResource
    {
        $restoredTask = $service->execute($taskId, $request->user()->id);

        return new TaskResource($restoredTask);
    }

    public function toggleStatus(int $taskId, Request $request, ToggleTaskStatusService $service): TaskResource
    {
        $updatedTask = $service->execute($taskId, $request->user());

        return new TaskResource($updatedTask);
    }
}

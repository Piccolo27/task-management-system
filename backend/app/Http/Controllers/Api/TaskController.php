<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\TaskServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TaskController extends Controller
{
    private TaskServiceInterface $taskService;

    /**
     * To create new instance of task controller
     *
     * @param TaskServiceInterface $taskService
     */
    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * To get tasks
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTasks(Request $request): JsonResponse
    {
        $status = $request->query('status') ?? null;
        return $this->taskService->getTasks($status);
    }

    /**
     * To get task
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function getTask(Task $task): JsonResponse
    {
        return $this->taskService->getTask($task);
    }

    /**
     * To create task
     *
     * @param CreateTaskRequest $request
     * @return JsonResponse
     */
    public function createTask(CreateTaskRequest $request): JsonResponse
    {
        return $this->taskService->createTask($request->validated());
    }

    /**
     * To update task
     *
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function updateTask(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        return $this->taskService->updateTask($request->validated(), $task);
    }

    /**
     * To delete task
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function deleteTask(Task $task): JsonResponse
    {
        return $this->taskService->deleteTask($task);
    }

    /**
     * To export tasks
     *
     * @return BinaryFileResponse
     */
    public function exportTasks(): BinaryFileResponse
    {
        return $this->taskService->exportTasks();
    }
}

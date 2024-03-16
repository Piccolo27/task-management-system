<?php

namespace App\Services;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Events\Task\TaskCreated;
use App\Events\Task\TaskUpdated;
use App\Exports\TasksExport;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TaskService implements TaskServiceInterface
{
    private TaskRepositoryInterface $taskRepository;

    /**
     * To create a new instance of task service
     *
     * @param TaskRepositoryInterface $taskRepository
     */
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * To get all tasks
     *
     * @param $status
     * @return JsonResponse
     */
    public function getTasks($status): JsonResponse
    {
        $authUserPosition = Auth::user()->position;
        try {
            if ($status == 'unclosed') {
                $tasks = $authUserPosition == config('constants.ADMIN')
                    ? $this->taskRepository->getNotClosedTasksForAdmin()
                    : $this->taskRepository->getNotClosedTasksForEmployee();
            } else {
                logger('all tasks');
                $tasks = $authUserPosition == config('constants.ADMIN')
                    ? $this->taskRepository->getTasksForAdmin()
                    : $this->taskRepository->getTasksForEmployee();
            }
            $response = response()->json(['tasks' => $tasks], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting all tasks: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.TASKS_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To get a task
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function getTask(Task $task): JsonResponse
    {
        try {
            $task = $this->taskRepository->getTask($task);
            $response = response()->json(['task' => $task], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting a task: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.TASK_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To create a task
     *
     * @param array $data
     * @return JsonResponse
     */
    public function createTask(array $data): JsonResponse
    {
        try {
            $task = $this->taskRepository->createTask($data);
            $this->sendTaskCreatedNotification($task);
            $response = response()->json([
                'message' => config('constants.success.TASK_CREATE')
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error("An exception occurred in creating a task: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.TASK_CREATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To send task created notification
     *
     * @param Task $task
     * @return void
     */
    public function sendTaskCreatedNotification(Task $task): void
    {
        $data = [
            'message' => Auth::user()->employee_name . ' created a new task for project named ' . $task->project->project_name,
            'created_by' => Auth::id(),
            'created_employee' => Auth::user(),
            'task_member_id' => $task->member->employee_id
        ];
        broadcast(new TaskCreated($data));
    }

    /**
     * To update a task
     *
     * @param array $data
     * @param Task $task
     * @return JsonResponse
     */
    public function updateTask(array $data, Task $task): JsonResponse
    {
        try {
            $task = $this->taskRepository->updateTask($data, $task);
            $this->sendTaskUpdatedNotification($task);
            $response = response()->json([
                'message' => config('constants.success.TASK_UPDATE')
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in updating a task: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.TASK_CREATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To send task updated notification
     *
     * @param Task $task
     * @return void
     */
    public function sendTaskUpdatedNotification(Task $task): void
    {
        $data = [
            'message' => Auth::user()->employee_name . ' updated a task titled ' . $task->title,
            'created_by' => Auth::id(),
            'created_employee' => Auth::user(),
            'task_member_id' => $task->member->employee_id
        ];
        broadcast(new TaskUpdated($data));
    }

    /**
     * To delete a task
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function deleteTask(Task $task): JsonResponse
    {
        try {
            $this->taskRepository->deleteTask($task);
            $response = response()->json([
                'message' => config('constants.messages.TASK_DELETE')
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in deleting a task: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.TASK_DELETE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To export tasks in excel
     *
     * @return BinaryFileResponse
     */
    public function exportTasks(): BinaryFileResponse
    {
        return Excel::download(new TasksExport, 'tasks.xlsx');
    }

    /**
     * To get the total count of not closed tasks
     *
     * @param string $role
     * @return int
     */
    public function getNotClosedTasksTotalCount(string $role): int
    {
        return $role == 'admin' ? $this->taskRepository->getNotClosedTasksTotalCount() :
                $this->taskRepository->getNotClosedTasksTotalCountForEmployee(Auth::id());
    }
}

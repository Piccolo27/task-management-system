<?php

namespace App\Repositories;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Get all tasks for admin
     *
     * @return Collection|array
     */
    public function getTasksForAdmin(): Collection|array
    {
        return Task::with([
            'project',
            'member'
        ])->orderBy('task_id', 'desc')->get();
    }

    /**
     * To get tasks for employee
     *
     * @return Collection|array
     */
    public function getTasksForEmployee(): Collection|array
    {
        return Task::where('assigned_member_id', Auth::user()->employee_id)
            ->with(['project','member'])
            ->orderBy('task_id', 'desc')
            ->get();
    }

    /**
     * To get task by id
     *
     * @param Task $task
     * @return Task
     */
    public function getTask(Task $task): Task
    {
        $task->load('project', 'member');
        return $task;
    }

    /**
     * To create task
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        return Task::create($data)->load('project', 'member');
    }

    /**
     * To update task
     *
     * @param array $data
     * @param Task $task
     * @return Task
     */
    public function updateTask(array $data, Task $task): Task
    {
        $task->project_id = $data['project_id'];
        $task->title = $data['title'];
        $task->description = $data['description'];
        $task->assigned_member_id = $data['assigned_member_id'];
        $task->estimate_hr = $data['estimate_hr'];
        $task->actual_hr = $data['actual_hr'];
        $task->status = $data['status'];
        $task->estimate_start_date = $data['estimate_start_date'];
        $task->estimate_finish_date = $data['estimate_finish_date'];
        $task->actual_start_date = $data['actual_start_date'];
        $task->actual_finish_date = $data['actual_finish_date'];
        $task->save();

        return $task->load('project', 'member');
    }

    /**
     * To delete task
     *
     * @param Task $task
     * @return void
     */
    public function deleteTask(Task $task): void
    {
        $task->delete();
    }

    /**
     * To get not closed tasks for admin
     *
     * @return Collection
     */
    public function getNotClosedTasksForAdmin(): Collection
    {
        $closedStatus = config('constants.TASK_CLOSED_STATUS');
        return Task::whereNot('status', $closedStatus)
            ->with(['project', 'member'])
            ->orderBy('task_id', 'asc')
            ->get();
    }

    /**
     * To get not closed tasks for employee
     *
     * @return Collection
     */
    public function getNotClosedTasksForEmployee(): Collection
    {
        $closedStatus = config('constants.TASK_CLOSED_STATUS');
        $currentEmployeeId = Auth::user()->employee_id;
        return Task::whereNot('status', $closedStatus)
            ->where('assigned_member_id', $currentEmployeeId)
            ->with(['project', 'member'])
            ->orderBy('task_id', 'asc')
            ->get();
    }

    /**
     * To get total count of not closed tasks
     *
     * @return int
     */
    public function getNotClosedTasksTotalCount(): int
    {
        $closedStatus = config('constants.TASK_CLOSED_STATUS');
        return Task::whereNot('status', $closedStatus)->count();
    }

    /**
     * To get total count of not closed tasks for employee
     *
     * @param int $employeeId
     * @return int
     */
    public function getNotClosedTasksTotalCountForEmployee(int $employeeId): int
    {
        $closedStatus = config('constants.TASK_CLOSED_STATUS');
        return Task::where('assigned_member_id', $employeeId)
            ->whereNot('status', $closedStatus)
            ->count();
    }
}

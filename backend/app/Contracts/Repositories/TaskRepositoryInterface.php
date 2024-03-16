<?php

namespace App\Contracts\Repositories;

use App\Models\Task;

interface TaskRepositoryInterface
{
    public function getTasksForAdmin();

    public function getTasksForEmployee();

    public function getTask(Task $task);

    public function createTask(array $data);

    public function updateTask(array $data, Task $task);

    public function deleteTask(Task $task);

    public function getNotClosedTasksForAdmin();

    public function getNotClosedTasksForEmployee();

    public function getNotClosedTasksTotalCount();

    public function getNotClosedTasksTotalCountForEmployee(int $employeeId);
}

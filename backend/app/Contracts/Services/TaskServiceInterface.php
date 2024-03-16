<?php

namespace App\Contracts\Services;

use App\Models\Task;

interface TaskServiceInterface
{
    public function getTasks($status);

    public function getTask(Task $task);

    public function createTask(array $data);

    public function updateTask(array $data, Task $task);

    public function deleteTask(Task $task);

    public function exportTasks();

    public function getNotClosedTasksTotalCount(string $role);
}

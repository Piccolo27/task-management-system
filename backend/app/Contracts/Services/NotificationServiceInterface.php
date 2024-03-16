<?php

namespace App\Contracts\Services;

interface NotificationServiceInterface
{

    public function createNotification(array $data);

    public function getNotificationsByEmployeeId(int $employeeId);

    public function deleteNotificationForEmployee(int $notiId, int $employeeId);
}

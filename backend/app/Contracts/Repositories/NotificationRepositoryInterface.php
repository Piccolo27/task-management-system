<?php

namespace App\Contracts\Repositories;

interface NotificationRepositoryInterface
{

    public function createNotification(array $data);

    public function getNotificationsByEmployeeId(int $employeeId);

    public function createNotificationReceiver(int $notificationId, int $notiReceiverId);

    public function changeVisibleStatusOfNotiForEmployee(int $notiId, int $employeeId);
}

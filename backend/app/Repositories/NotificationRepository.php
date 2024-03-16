<?php

namespace App\Repositories;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Models\EmployeeNotification;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{

    /**
     * To create notification
     *
     * @param array $data
     * @return Notification
     */
    public function createNotification(array $data): Notification
    {
        return Notification::create($data);
    }

    /**
     * To get notifications by employee id
     *
     * @param int $employeeId
     * @return Collection|array
     */
    public function getNotificationsByEmployeeId(int $employeeId): Collection|array
    {
        return Notification::whereHas('receivers', function ($query) use ($employeeId) {
            $query->where('employees.employee_id', $employeeId)
                ->where('employee_notification.is_visible', true);
        })->with([
            'createdEmployee',
        ])->orderByDesc('created_at')
          ->get();
    }

    /**
     * To create notification receiver
     *
     * @param int $notificationId
     * @param int $notiReceiverId
     * @return void
     */
    public function createNotificationReceiver(int $notificationId, int $notiReceiverId): void
    {
        EmployeeNotification::create([
            'notification_id' => $notificationId,
            'employee_id' => $notiReceiverId,
        ]);
    }

    /**
     * To change visible status of notification for employee
     *
     * @param int $notiId
     * @param int $employeeId
     * @return void
     */
    public function changeVisibleStatusOfNotiForEmployee(int $notiId, int $employeeId): void
    {
        $employeeNotification = EmployeeNotification::where('notification_id', $notiId)
            ->where('employee_id', $employeeId)
            ->first();

        $employeeNotification->is_visible = false;
        $employeeNotification->save();
    }
}

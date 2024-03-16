<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    private NotificationServiceInterface $notificationService;

    /**
     * To create new instance of notification controller
     *
     * @param NotificationServiceInterface $notificationService
     */
    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * To get notifications by employee id
     *
     * @param int $employeeId
     * @return JsonResponse
     */
    public function getNotificationsByEmployeeId(int $employeeId): JsonResponse
    {
        return $this->notificationService->getNotificationsByEmployeeId($employeeId);
    }

    /**
     * To delete notification for employee
     *
     * @param int $notiId
     * @param int $employeeId
     * @return JsonResponse
     */
    public function deleteNotificationForEmployee(int $notiId, int $employeeId): JsonResponse
    {
        return $this->notificationService->deleteNotificationForEmployee($notiId, $employeeId);
    }
}

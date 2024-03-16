<?php

namespace App\Services;

use App\Contracts\Repositories\EmployeeRepositoryInterface;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Services\NotificationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NotificationService implements NotificationServiceInterface
{
    private NotificationRepositoryInterface $notificationRepository;
    private EmployeeRepositoryInterface $employeeRepository;

    /**
     * To create a new instance of notification service
     *
     * @param NotificationRepositoryInterface $notificationRepository
     * @param EmployeeRepositoryInterface $employeeRepository
     */
    public function __construct(
        NotificationRepositoryInterface $notificationRepository,
        EmployeeRepositoryInterface $employeeRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->employeeRepository = $employeeRepository;
    }


    /**
     * To create notification
     *
     * @param array $data
     * @return void
     */
    public function createNotification(array $data): void
    {
        try {
            $notification = $this->notificationRepository->createNotification($data);
            $notiReceiversIds = $this->employeeRepository->getAdminsIdsExceptCurrent();

            if (isset($data['task_member_id'])) {
                $notiReceiversIds[] = $data['task_member_id'];
            }

            foreach ( $notiReceiversIds as $notiReceiverId ) {
                $this->notificationRepository->createNotificationReceiver($notification->id, $notiReceiverId);
            }
        } catch (\Exception $e) {
            Log::error("An exception occurred in creating notification: " . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * To get notifications by employee id
     *
     * @param int $employeeId
     * @return JsonResponse
     */
    public function getNotificationsByEmployeeId(int $employeeId): JsonResponse
    {
        try {
            $notifications = $this->notificationRepository->getNotificationsByEmployeeId($employeeId);
            $response = response()->json([
                'notifications' => $notifications
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting notifications: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.NOTIFICATION_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
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
        try {
            $this->notificationRepository->changeVisibleStatusOfNotiForEmployee($notiId, $employeeId);
            $response = response()->json([
                'message' => config('constants.success.NOTIFICATION_DELETE')
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in deleting notifications: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.NOTIFICATION_DELETE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }
}

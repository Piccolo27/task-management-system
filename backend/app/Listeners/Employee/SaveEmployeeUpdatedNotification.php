<?php

namespace App\Listeners\Employee;

use App\Contracts\Services\NotificationServiceInterface;
use App\Events\Employee\EmployeeUpdated;

class SaveEmployeeUpdatedNotification
{
    protected NotificationServiceInterface $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(EmployeeUpdated $event): void
    {
        $this->notificationService->createNotification($event->noti);
    }
}

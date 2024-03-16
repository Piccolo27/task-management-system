<?php

namespace App\Listeners\Employee;

use App\Contracts\Services\NotificationServiceInterface;
use App\Events\Employee\EmployeeCreated;

class SaveEmployeeCreatedNotification
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
    public function handle(EmployeeCreated $event): void
    {
        $this->notificationService->createNotification($event->noti);
    }
}

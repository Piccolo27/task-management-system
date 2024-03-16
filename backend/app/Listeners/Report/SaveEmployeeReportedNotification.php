<?php

namespace App\Listeners\Report;

use App\Contracts\Services\NotificationServiceInterface;
use App\Events\Report\EmployeeReported;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveEmployeeReportedNotification
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
    public function handle(EmployeeReported $event): void
    {
        $this->notificationService->createNotification($event->noti);
    }
}

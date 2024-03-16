<?php

namespace App\Listeners\Project;

use App\Contracts\Services\NotificationServiceInterface;
use App\Events\Project\ProjectDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveProjectDeletedNotification
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
    public function handle(ProjectDeleted $event): void
    {
        $this->notificationService->createNotification($event->noti);
    }
}

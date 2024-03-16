<?php

namespace App\Listeners\Project;

use App\Contracts\Services\NotificationServiceInterface;
use App\Events\Project\ProjectUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveProjectUpdatedNotification
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
    public function handle(ProjectUpdated $event): void
    {
        $this->notificationService->createNotification($event->noti);
    }
}

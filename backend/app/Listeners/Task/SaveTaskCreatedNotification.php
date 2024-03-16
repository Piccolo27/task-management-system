<?php

namespace App\Listeners\Task;

use App\Contracts\Services\NotificationServiceInterface;
use App\Events\Task\TaskCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveTaskCreatedNotification
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
    public function handle(TaskCreated $event): void
    {
        $this->notificationService->createNotification($event->noti);
    }
}

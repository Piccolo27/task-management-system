<?php

namespace App\Providers;

use App\Events\Employee\EmployeeCreated;
use App\Events\Employee\EmployeeDeleted;
use App\Events\Employee\EmployeeUpdated;
use App\Events\Project\ProjectCreated;
use App\Events\Project\ProjectDeleted;
use App\Events\Project\ProjectUpdated;
use App\Events\Report\EmployeeReported;
use App\Events\Task\TaskCreated;
use App\Events\Task\TaskUpdated;
use App\Listeners\Employee\SaveEmployeeCreatedNotification;
use App\Listeners\Employee\SaveEmployeeDeletedNotification;
use App\Listeners\Employee\SaveEmployeeUpdatedNotification;
use App\Listeners\Project\SaveProjectCreatedNotification;
use App\Listeners\Project\SaveProjectDeletedNotification;
use App\Listeners\Project\SaveProjectUpdatedNotification;
use App\Listeners\Report\SaveEmployeeReportedNotification;
use App\Listeners\Task\SaveTaskCreatedNotification;
use App\Listeners\Task\SaveTaskUpdatedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        EmployeeCreated::class => [
            SaveEmployeeCreatedNotification::class
        ],
        EmployeeUpdated::class => [
            SaveEmployeeUpdatedNotification::class
        ],
        EmployeeDeleted::class => [
            SaveEmployeeDeletedNotification::class
        ],
        ProjectCreated::class => [
            SaveProjectCreatedNotification::class
        ],
        ProjectUpdated::class => [
            SaveProjectUpdatedNotification::class
        ],
        ProjectDeleted::class => [
            SaveProjectDeletedNotification::class
        ],
        EmployeeReported::class => [
            SaveEmployeeReportedNotification::class
        ],
        TaskCreated::class => [
            SaveTaskCreatedNotification::class
        ],
        TaskUpdated::class => [
            SaveTaskUpdatedNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Queue::failing(function (JobFailed $event) {
            Log::error("Job failed on queue {$event->connectionName} with message: {$event->exception->getMessage()}");
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

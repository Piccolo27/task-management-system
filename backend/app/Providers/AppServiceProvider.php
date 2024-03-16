<?php

namespace App\Providers;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Repositories\ReportRepositoryInterface;
use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\ReportServiceInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Repositories\DmRepository;
use App\Repositories\AuthRepository;
use App\Repositories\DmReplyRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ReportRepository;
use App\Repositories\TaskRepository;
use App\Services\DashboardService;
use App\Services\DmService;
use App\Services\AuthService;
use App\Services\MailService;
use App\Services\DmReplyService;
use App\Services\EmployeeService;
use App\Contracts\Repositories\DmRepositoryInterface;
use App\Contracts\Repositories\AuthRepositoryInterface;
use App\Services\NotificationService;
use App\Services\ProjectService;
use App\Services\ReportService;
use App\Services\TaskService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\DmReplyRepositoryInterface;
use App\Contracts\Repositories\EmployeeRepositoryInterface;
use App\Contracts\Services\DmServiceInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\MailServiceInterface;
use App\Contracts\Services\DmReplyServiceInterface;
use App\Contracts\Services\EmployeeServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $binds = [
            AuthServiceInterface::class => AuthService::class,
            AuthRepositoryInterface::class => AuthRepository::class,
            EmployeeServiceInterface::class => EmployeeService::class,
            EmployeeRepositoryInterface::class => EmployeeRepository::class,
            ProjectServiceInterface::class => ProjectService::class,
            ProjectRepositoryInterface::class => ProjectRepository::class,
            TaskServiceInterface::class => TaskService::class,
            TaskRepositoryInterface::class => TaskRepository::class,
            ReportServiceInterface::class => ReportService::class,
            ReportRepositoryInterface::class => ReportRepository::class,
            DmServiceInterface::class => DmService::class,
            DmRepositoryInterface::class => DmRepository::class,
            DmReplyServiceInterface::class => DmReplyService::class,
            DmReplyRepositoryInterface::class => DmReplyRepository::class,
            MailServiceInterface::class => MailService::class,
            NotificationServiceInterface::class => NotificationService::class,
            NotificationRepositoryInterface::class => NotificationRepository::class,
            DashboardServiceInterface::class => DashboardService::class,
        ];

        foreach ($binds as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\DmReply;
use App\Models\Notification;
use App\Models\Project;
use App\Policies\DmReplyPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\ProjectPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        DmReply::class => DmReplyPolicy::class,
        Notification::class => NotificationPolicy::class,
        Project::class => ProjectPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return env("FRONTEND_URL") . "/reset-password?token=" . $token;
        });
    }
}

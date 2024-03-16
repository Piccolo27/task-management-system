<?php

namespace App\Broadcasting;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class AdminNotificationChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(Employee $user): array|bool
    {
        $currentUser = Auth::user();

        if ($currentUser->position == config('constants.ADMIN')) {
            return true;
        }
        return false;
    }
}

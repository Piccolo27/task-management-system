<?php

namespace App\Policies;

use App\Models\DirectMessage;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Auth\Access\Response;

class DirectMessagePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, DirectMessage $directMessage): bool
    {
        $currentDatetime = Carbon::now();
        $startDatetime = Carbon::parse($directMessage->start_at);

        return $directMessage->owner->employee_id == $employee->employee_id && $startDatetime->isAfter($currentDatetime);
    }
}

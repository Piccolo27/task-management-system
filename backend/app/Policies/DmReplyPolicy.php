<?php

namespace App\Policies;

use App\Models\DmReply;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class DmReplyPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, DmReply $dmReply): bool
    {
        $dmReply->load('createdUser');
        return $employee->employee_id === $dmReply->createdUser->employee_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, DmReply $dmReply): bool
    {
        $dmReply->load('createdUser');
        return $employee->employee_id === $dmReply->createdUser->employee_id;
    }
}

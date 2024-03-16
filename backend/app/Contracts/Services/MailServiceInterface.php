<?php

namespace App\Contracts\Services;

use App\Models\Employee;

interface MailServiceInterface
{
    public function sendEmployeeCreateMail(Employee $employee);

    public function sendNotReportedMail(Employee $admin, $notReportedMembers);
}

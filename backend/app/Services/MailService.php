<?php

namespace App\Services;

use App\Contracts\Services\MailServiceInterface;
use App\Mail\EmployeeCreated;
use App\Mail\NonReportMemberList;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;

class MailService implements MailServiceInterface
{
    /**
     * To send mail to new employee
     *
     * @param Employee $employee
     * @return void
     */
    public function sendEmployeeCreateMail(Employee $employee): void
    {
        Mail::to($employee->email)->send(new EmployeeCreated($employee));
    }

    /**
     * To send mail to admin about not reported members
     *
     * @param Employee $admin
     * @param $notReportedMembers
     * @return void
     */
    public function sendNotReportedMail(Employee $admin, $notReportedMembers): void
    {
        Mail::to($admin->email)->send(new NonReportMemberList($admin, $notReportedMembers));
    }
}

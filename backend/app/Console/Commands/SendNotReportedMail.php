<?php

namespace App\Console\Commands;

use App\Contracts\Repositories\EmployeeRepositoryInterface;
use App\Contracts\Services\MailServiceInterface;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendNotReportedMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:not-reported';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email with list of not reported members to all admins';

    private EmployeeRepositoryInterface $employeeRepository;
    private MailServiceInterface $mailService;

    public function __construct(
        EmployeeRepositoryInterface $employeeRepository,
        MailServiceInterface $mailService
    ) {
        parent::__construct();
        $this->employeeRepository = $employeeRepository;
        $this->mailService = $mailService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $members = $this->employeeRepository->getEmployees();
        $notReportedMembers = $members->filter(function ($member) {
           $today = Carbon::today();
           return !Report::where('reported_by', $member->employee_id)
               ->whereDate('created_at', $today)
               ->exists();
        });

        if (!$notReportedMembers->isEmpty()) {
            $admins = $this->employeeRepository->getAdmins();
            foreach ($admins as $admin) {
                $this->mailService->sendNotReportedMail($admin, $notReportedMembers);
            }
        }
    }
}

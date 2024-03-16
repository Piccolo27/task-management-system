<?php

namespace App\Services;

use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\EmployeeServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\ReportServiceInterface;
use App\Contracts\Services\TaskServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardService implements DashboardServiceInterface
{
    private EmployeeServiceInterface $employeeService;
    private ProjectServiceInterface $projectService;
    private TaskServiceInterface $taskService;
    private ReportServiceInterface $reportService;

    /**
     * To create a new DashboardService instance
     *
     * @param EmployeeServiceInterface $employeeService
     * @param ProjectServiceInterface $projectService
     * @param TaskServiceInterface $taskService
     * @param ReportServiceInterface $reportService
     */
    public function __construct
    (
        EmployeeServiceInterface $employeeService,
        ProjectServiceInterface $projectService,
        TaskServiceInterface $taskService,
        ReportServiceInterface $reportService
    ) {
        $this->employeeService = $employeeService;
        $this->projectService = $projectService;
        $this->taskService = $taskService;
        $this->reportService = $reportService;
    }

    /**
     * To get dashboard statistics
     *
     * @return JsonResponse
     */
    public function getDashboardStatistics(): JsonResponse
    {
        $ADMIN = config('constants.ADMIN');
        try {
            if (Auth::user()->position == $ADMIN) {
                $data = [
                    'employees' => $this->employeeService->getEmployeesTotalCount(),
                    'projects' => $this->projectService->getProjectsTotalCount(),
                    'not_closed_tasks' => $this->taskService->getNotClosedTasksTotalCount('admin'),
                    'reports' => $this->reportService->getReportsTotalCount(),
                ];
            } else {
                $data = [
                    'not_closed_tasks' => $this->taskService->getNotClosedTasksTotalCount('employee'),
                    'reports' => $this->reportService->getReportsTotalCount()
                ];
            }

            $response = response()->json(['statistics' => $data], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $response = response()->json([
                'error' => 'Unexpected error occurred while getting dashboard statistics'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}

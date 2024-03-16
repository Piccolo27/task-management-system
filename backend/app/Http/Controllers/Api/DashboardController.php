<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\EmployeeServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\ReportServiceInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private DashboardServiceInterface $dashboardService;

    /**
     * To create new instance of DashboardController
     *
     * @param DashboardServiceInterface $dashboardService
     */
    public function __construct (DashboardServiceInterface $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * To get dashboard statistics
     *
     * @return JsonResponse
     */
    public function getDashboardStatistics(): JsonResponse
    {
        return $this->dashboardService->getDashboardStatistics();
    }
}

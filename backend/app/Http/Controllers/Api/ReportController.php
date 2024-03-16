<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ReportServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateReportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    private ReportServiceInterface $reportService;

    /**
     * To create a new instance of ReportController
     *
     * @param ReportServiceInterface $reportService
     */
    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * To create a new report
     *
     *
     * @param CreateReportRequest $request
     * @return mixed
     */
    public function createReport(CreateReportRequest $request): JsonResponse
    {
        return $this->reportService->createReport($request->validated());
    }

    /**
     * To get all reports
     *
     * @return JsonResponse
     */
    public function getReports(): JsonResponse
    {
        return $this->reportService->getReports();
    }

    /**
     * To export all reports in excel format
     *
     * @return BinaryFileResponse
     */
    public function exportReports(): BinaryFileResponse
    {
        return $this->reportService->getExcelForAllReports();
    }
}

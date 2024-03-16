<?php

namespace App\Services;

use App\Contracts\Repositories\ReportRepositoryInterface;
use App\Contracts\Services\ReportServiceInterface;
use App\Events\Report\EmployeeReported;
use App\Exports\ReportsExport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportService implements ReportServiceInterface
{
    private ReportRepositoryInterface $reportRepository;

    /**
     * To create a new instance of report service
     *
     * @param ReportRepositoryInterface $reportRepository
     */
    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * To create a report
     *
     * @param array $data
     * @return JsonResponse
     */
    public function createReport(array $data): JsonResponse
    {
        $report =  [
            'description' => $data['description'],
            'report_to' => $data['report_to'],
            'reported_by' => auth()->id(),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'date' => Carbon::now()
        ];

        try {
            $this->reportRepository->createReport($report);
            $this->sendEmployeeReportedNotification($report['report_to']);
            $response = response()->json([
                'message' => config('constants.messages.REPORT_CREATE')
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error("An exception occurred in creating report: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.REPORT_CREATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To send employee reported notification to client side
     *
     * @param int $reportTo
     * @return void
     */
    private function sendEmployeeReportedNotification(int $reportTo): void
    {
        $data = [
            'message' => Auth::user()->employee_name . " has reported in " . Carbon::now()->format('d M Y h:i:s A'),
            'created_by' => Auth::id(),
            'created_employee' => Auth::user(),
            'report_to' => $reportTo
        ];
        broadcast(new EmployeeReported($data));
    }

    /**
     * To get reports
     *
     * @return JsonResponse
     */
    public function getReports(): JsonResponse
    {
        try {
            $reports = $this->reportRepository->getReports();
            $response = response()->json(['reports' => $reports], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting reports: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.REPORTS_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To get excel for all reports
     *
     * @return BinaryFileResponse
     */
    public function getExcelForAllReports(): BinaryFileResponse
    {
        return Excel::download(new ReportsExport, 'reports.xlsx');
    }

    /**
     * To get total count of reports
     *
     * @return int
     */
    public function getReportsTotalCount(): int
    {
        return $this->reportRepository->getReportsTotalCount();
    }
}

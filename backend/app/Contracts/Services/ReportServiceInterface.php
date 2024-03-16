<?php

namespace App\Contracts\Services;

interface ReportServiceInterface
{
    public function createReport(array $data);

    public function getReports();

    public function getExcelForAllReports();

    public function getReportsTotalCount();
}

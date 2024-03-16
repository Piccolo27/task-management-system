<?php

namespace App\Contracts\Repositories;

interface ReportRepositoryInterface
{
    public function createReport(array $data);

    public function getReports();

    public function getReportsTotalCount();
}

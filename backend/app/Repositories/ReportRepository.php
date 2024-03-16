<?php

namespace App\Repositories;

use App\Contracts\Repositories\ReportRepositoryInterface;
use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * To get all reports
     *
     * @return Collection|array
     */
    public function getReports(): Collection|array
    {
        return Report::with(['admin', 'reporter'])
            ->orderBy('report_id', 'desc')
            ->get();
    }

    /**
     * To create report
     *
     * @param array $data
     * @return void
     */
    public function createReport(array $data): void
    {
        Report::create($data);
    }

    /**
     * To get total count of reports
     *
     * @return int
     */
    public function getReportsTotalCount(): int
    {
        return Report::count();
    }
}

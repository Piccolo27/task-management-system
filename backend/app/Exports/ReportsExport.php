<?php

namespace App\Exports;

use App\Models\Report;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return Report::with('admin', 'reporter')
            ->orderBy('report_id', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Description',
            'Report to',
            'Reported by',
            'Created at',
            'Updated at'
        ];
    }

    /**
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->report_id,
            $row->date,
            $row->description,
            $row->admin->employee_name,
            $row->reporter->employee_name,
            $row->created_at,
            $row->updated_at
        ];
    }
}

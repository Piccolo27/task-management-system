<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TasksExport implements FromCollection, WithHeadings
{
    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return Auth::user()->position == config('constants.ADMIN')
            ? Task::with('project', 'member')->get()
            : Task::where('assigned_member_id', Auth::user()->employee_id)->with('project', 'member');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Project ID',
            'Title',
            'Description',
            'Assigned Member ID',
            'Estimate Hours',
            'Actual Hours',
            'Status',
            'Estimate Start Date',
            'Estimate Finish Date',
            'Actual Start Date',
            'Actual Finish Date',
            'Created At',
            'Updated At',
        ];
    }
}

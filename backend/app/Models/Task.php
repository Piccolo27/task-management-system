<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'task_id';

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'assigned_member_id',
        'estimate_hr',
        'actual_hr',
        'status',
        'estimate_start_date',
        'estimate_finish_date',
        'actual_start_date',
        'actual_finish_date'
    ];

    /**
     * To relation with project model
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * To relation with employee model
     *
     * @return BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_member_id', 'employee_id');
    }
}

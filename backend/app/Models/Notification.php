<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'created_by'];

    /**
     * Get created admin of the notification
     *
     * @return BelongsTo
     */
    public function createdEmployee(): belongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by', 'employee_id');
    }

    /**
     * Get all the receivers of the notification
     *
     * @return BelongsToMany
     */
    public function receivers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_notification','notification_id','employee_id');
    }
}

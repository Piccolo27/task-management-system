<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeNotification extends Model
{
    use HasFactory;

    protected $table = 'employee_notification';
    protected $fillable = [
        'employee_id',
        'notification_id',
        'is_visible',
    ];
}

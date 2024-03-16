<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThreadEmployee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'thread_employee';

    protected $fillable = ['dm_thread_id', 'employee_id', 'user_unread'];
}

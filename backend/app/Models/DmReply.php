<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DmReply extends Model
{
    use HasFactory;

    protected $primaryKey = 'dm_reply_id';

    protected $fillable = [
        'dm_thread_id',
        'body',
        'created_by',
        'updated_by'
    ];

    /**
     * Get the createdUser that owns the DmReply
     *
     * @return BelongsTo
     */
    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by', 'employee_id');
    }

    /**
     * Get the dm thread that owns the DmReply
     *
     * @return BelongsTo
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(DmThread::class, 'dm_thread_id');
    }
}

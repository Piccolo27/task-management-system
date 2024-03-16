<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DirectMessage extends Model
{
    use HasFactory;

    protected $primaryKey = 'direct_message_id';

    protected $fillable = [
        'owner_id',
        'title',
        'body',
        'replyable',
        'start_at',
        'created_by',
        'updated_by'
    ];

    /**
     * Get the direct message thread associated with the DirectMessage
     *
     * @return HasOne
     */
    public function dmThread(): HasOne
    {
        return $this->hasOne(DmThread::class, 'direct_message_id');
    }

    /**
     * Get the owner(sender) associated with the DirectMessage
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Employee::class,'owner_id', 'employee_id');
    }
}

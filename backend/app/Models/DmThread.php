<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DmThread extends Model
{
    use HasFactory;

    protected $primaryKey = 'dm_thread_id';

    protected $fillable = [
        'direct_message_id',
        'replyable',
        'owner_unread',
        'user_unread',
        'dm_updated',
        'created_by'
    ];

    /**
     * Get the direct message associated with the DmThread
     *
     * @return HasOne
     */
    public function dm(): HasOne
    {
        return $this->hasOne(DirectMessage::class, 'direct_message_id', 'direct_message_id');
    }

    /**
     * Get all the dmReplys for the DmThread
     *
     * @return HasMany
     */
    public function dmReplys(): HasMany
    {
        return $this->hasMany(DmReply::class, 'dm_thread_id');
    }

    /**
     * Get all the members that belong to the dm thread
     *
     * @return BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'thread_employee','dm_thread_id','employee_id');
    }
}

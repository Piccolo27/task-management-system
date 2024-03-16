<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'project_id';
    protected $fillable = [
        'project_name',
        'language',
        'description',
        'start_date',
        'end_date'
    ];

    /**
     * To delete all tasks when project deleted
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::deleting(function ($project) {
            foreach ($project->tasks as $task) {
                $task->delete();
            }
        });
    }

    /**
     * To relation with task model
     *
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id', 'project_id');
    }
}

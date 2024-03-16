<?php

namespace App\Models;

use App\Traits\AvoidDuplicateConstraintSoftDelete;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employee extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, SoftDeletes, Notifiable, AvoidDuplicateConstraintSoftDelete;

    /**
     * This is important for JWT token
     * because we change the primary key from default id to employee_id
     * @var string
     */
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'employee_name',
        'email',
        'password',
        'profile',
        'position',
        'address',
        'dob',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'id' => $this->employee_id,
            'name' => $this->employee_name,
            'email' => $this->email,
            'position' => $this->position,
            'profile' => $this->profile
        ];
    }

    /**
     * To get duplicate avoid columns
     *
     * @return string[]
     */
    public function getDuplicateAvoidColumns() : array
    {
        return [
            'email'
        ];
    }

    /**
     * To relation with task model
     *
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_member_id', 'employee_id');
    }

    /**
     * To get all dm threads that belongs to the employee
     *
     * @return BelongsToMany
     */
    public function dmThreads(): BelongsToMany
    {
        return $this->belongsToMany(DmThread::class, 'thread_employee');
    }

    /**
     * To get all notifications that belongs to the employee
     *
     * @return BelongsToMany
     */
    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'employee_notification', 'employee_id', 'notification_id');
    }
}

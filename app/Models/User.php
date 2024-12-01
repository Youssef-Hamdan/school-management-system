<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, LogsActivity;
    
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'is_active',
        'portal_id',
        'date_of_birth',
        'profile_image',
        'email_verified_at',
        'user_role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
    ];

    protected $dates = ['deleted_at'];
  

  // ===== Scoops
    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    

  // ===== Functions
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }
    protected static $logAttributes = ['first_name', 'last_name', 'email', 'is_active'];

    protected static $logOnlyDirty = true;

    function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
        ->setDescriptionForEvent(fn () => 'User has been updated successfully')
        ->useLogName('user_management')
        ->logAll()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();

    }

    protected static $logName = 'user-activity';

 
    public function getDescriptionForEvent(string $eventName): string
    {
        return "A user has been {$eventName}";
    }
  //

  // ===== Relations

    public function portal()
    {
        return $this->belongsTo(Portal::class, 'portal_id');
    }

    public function userRole()
    {
        return $this->belongsTo(Role::class, 'user_role_id');
    }

    public function coursesAsInstructor()
    {
        return $this->hasMany(Course::class, 'instructor_id') 
                    ->whereHas('user', function ($query) {
                        $query->where('user_role_id', 2);
                    });
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id')
                    ->whereHas('user', function ($query) {
                        $query->where('user_role_id', 3);
                    });
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id')
                    ->whereHas('user', function ($query) {
                        $query->where('user_role_id', 3);
                    });
    }

  //

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_name',
        'instructor_id',
        'is_online',
        'schedule_id'
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function classCourse()
    {
        return $this->belongsTo(ClassCourse::class, 'course_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'course_id');
    }
}

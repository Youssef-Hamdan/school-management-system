<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_id',
        'course_id'
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'id');
    }
}

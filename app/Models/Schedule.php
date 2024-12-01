<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'days',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'days' => 'array', 
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'schedule_id');
    }
}

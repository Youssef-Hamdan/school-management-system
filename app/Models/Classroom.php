<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_grade',
        'section_number',
        'capacity',
        'location'
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'classroom_id');
    }
}

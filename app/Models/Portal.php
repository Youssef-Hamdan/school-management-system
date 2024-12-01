<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Relationship with users.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

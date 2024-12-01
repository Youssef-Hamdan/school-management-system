<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes; // Import the SoftDeletes trait
use Spatie\Permission\Models\Permission as SpatiePermission; // Import Spatie's Permission model

class Permission extends SpatiePermission
{
    use SoftDeletes; // Enable soft deletes

    protected $fillable = [
        'name',
        'guard_name',
    ];
}

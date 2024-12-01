<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthenticationController as AdminAuthController;
use App\Http\Controllers\User\AuthenticationController as UserAuthController; 

Route::get('/', function () {
    return view('welcome');
});


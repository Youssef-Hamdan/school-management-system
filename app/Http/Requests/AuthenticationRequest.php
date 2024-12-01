<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\User\AuthenticationController as User;
use App\Http\Controllers\Admin\AuthenticationController as Admin;

class AuthenticationRequest extends FormRequest
{

    function login()
    {
        return Validator::make(request()->all(),[
            'email'         => ['required','email','exists:users,email,deleted_at,NULL'],
            'password'      => ['required'],
        ]);
    }

    function registration()
    {Log::info('Full Request Data: ', request()->all());     
       return Validator::make(request()->all(),[
            'first_name'        => 'required|string|max:30',
            'last_name'         => 'required|string|max:30',
            'date_of_birth'     => 'required|date|before:today',
            'profile_image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'email'             => 'required|email|unique:users,email',
            'password'          => ['required', Password::min(8)->mixedCase()->numbers()->symbols()],
            'user_role_id'      => 'required|integer|exists:roles,id',
            'portal_id'         => 'required|integer|exists:portals,id',
            'is_active'         => 'required|boolean',
        ]);
        // Log::info('Validation Errors: ', $validator->errors()->toArray());
    }
}

<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UserManagementRequest extends FormRequest
{

    function showUser($id)
    {   request()->merge(['id' => $id]);
        return Validator::make(request()->all(), [
            'id'                           =>'required|exists:users,id,deleted_at,NULL',
        ]);
    }

    function usersChart()
    {   
        return Validator::make(request()->all(), [
            'user_role_id'                  => 'required|integer|exists:roles,id',
            'start_date'                    => 'required|date_format:Y-m-d H:i:s',
            'end_date'                      => 'required|after:start_date|date_format:Y-m-d H:i:s',
        ]);
    }

    function getUsersByRole($user_role_id)
    {   request()->merge(['user_role_id' => $user_role_id]);
        return Validator::make(request()->all(), [
            'user_role_id'            =>'required|exists:roles,id'
        ]);
    }
    function getUsersBySearch()
    {   
        return Validator::make(request()->all(), [
            'search'            =>'nullable|string|max:254'
        ]);
    }
    
    function updateUserInfo($id)
    {   request()->merge(['id' => $id]);
        return Validator::make(request()->all(), [
            'id'                            => 'required|integer|exists:users,id',
            'first_name'                    => 'required|string|max:30',
            'last_name'                     => 'required|string|max:30',
            'date_of_birth'                 => 'required|date|before:today',
            'profile_image'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'email'                         => 'required|email|unique:users,email,'.$id,
            'password'                      => ['required',Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);
    }
    
    function updateUserStatus($id)
    {    request()->merge(['id' => $id]);
        return Validator::make(request()->all(), [
            'id'                            => 'required|integer|exists:users,id,deleted_at,NULL',
            'is_active'                     => 'required|boolean'
        ]);
    }
}


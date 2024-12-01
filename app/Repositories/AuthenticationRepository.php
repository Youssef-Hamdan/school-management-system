<?php

namespace App\Repositories;

use App\Models\User;
use App\Interface\AuthenticationInterface;


class AuthenticationRepository implements AuthenticationInterface
{
    // ----- register a user
    function store($request){

        return User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'password'          => bcrypt($request->password), 
            'date_of_birth'     => $request->date_of_birth,
            'profile_image'     => $request->profile_image,
            'user_role_id'      => $request->user_role_id,
            'portal_id'         => $request->portal_id,
            'is_active'         => $request->is_active
        ]);
    }
                        
}


<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class GradeManagementRequest extends FormRequest
{
    function index($student_id)
    {   request()->merge(['student_id'=>$student_id]);
        return Validator::make(request()->all(),[
            'student_id'        =>'required|integer|exists:users,id,deleted_at,NULL,user_role_id,3||exists:grades,student_id'
        ]);
    }
    function isSubmit()
    {   
        return Validator::make(request()->all(),[
            'id'                =>'required|integer|exists:grades,id,deleted_at,NULL',
            'is_active'         =>'required|boolean'
        ]);
    }

    function store()
    {   
        return Validator::make(request()->all(),[
            'assessment_id'     => 'required|integer|exists:assessments,id,deleted_at,NULL',
            'student_id'        => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,3',
            'grade'             => 'required|integer|max:100|min:0',
            
        ]);
    }
    function update($id,$instructor_id)
    {    request()->merge(['instructor_id'=>$instructor_id, 'id' => $id]);
        return Validator::make(request()->all(),[
            'id'                => 'required|integer|exists:grades,id,deleted_at,NULL',
            'instructor_id'     => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,2',
            'grade'             => 'required|integer|max:100|min:0',

        ]);
    }
    function delete($id)
    {    request()->merge(['id'=>$id]);
        return Validator::make(request()->all(),[
            'id'                => 'required|integer|exists:grades,id,deleted_at,NULL',
        ]);
    }
}

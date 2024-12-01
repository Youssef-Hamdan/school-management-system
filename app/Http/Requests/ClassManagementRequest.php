<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ClassManagementRequest extends FormRequest
{
    function store()
    {   
        return Validator::make(request()->all(), [
            'class_grade'     => 'required|string|max:255',
            'section_number'  => 'required|string|max:255',
            'capacity'        => 'required|integer|min:1',
            'location'        => 'required|string|max:255',
        ]);
    }
    function enroll($student_id)
    {   request()->merge(['student_id'=>$student_id]);
        return Validator::make(request()->all(), [
           'classroom_id'     => 'required|integer|exists:classrooms,id,deleted_at,NULL',
           'student_id'       => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,3',
        ]);
    }

    function addCourseToClass(){
        return Validator::make(request()->all(), [
           'class_id'      => 'required|integer|exists:classrooms,id,deleted_at,NULL',
           'course_id'     => 'required|integer|exists:courses,id,deleted_at,NULL',

        ]);
    }

    function update($id)
    {
        request()->merge(['id'=>$id]);
        return Validator::make(request()->all(), [
            'id'              => 'required|integer|exists:classrooms,id,deleted_at,NULL',
            'class_grade'     => 'required|string|max:255',
            'section_number'  => 'required|string|max:255',
            'capacity'        => 'required|integer|min:1',
            'location'        => 'required|string|max:255',
        ]);
    }
    function delete($id)
    {
        request()->merge(['id'=>$id]);
        return Validator::make(request()->all(), [
            'id'              => 'required|integer|exists:classrooms,id,deleted_at,NULL',
        ]);
    }

}

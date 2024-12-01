<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AssessmentManagementRequest extends FormRequest
{
    function indexSudentAssessment($student_id)
    {   request()->merge(['student_id'=>$student_id]);
        return Validator::make(request()->all(), [
            'student_id'     => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,3',
        ]);
    }

    function indexInstructorAssessment($instructor_id)
    {   request()->merge(['instructor_id'=>$instructor_id]);
        return Validator::make(request()->all(), [
            'instructor_id'     => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,2',
        ]);
    }

    function store($instructor_id)
    {   Log::debug('request ::',request()->all());
        request()->merge(['instructor_id'=>$instructor_id]);
        return Validator::make(request()->all(), [
            'instructor_id'  => 'required|integer|exists:courses,instructor_id,deleted_at,NULL',
            'course_id'     => 'required|integer|exists:courses,id,deleted_at,NULL',
            'type'          => 'required|in:Exam,Quiz,Assignment',
            'description'   => 'required|string|max:254',
            'percentage'    => 'required|integer|max:100|min:1',
            'due_date'      => 'required|date|after:today',

        ]);
    }

    function update($id)
    {   request()->merge(['id'=>$id]);
        return Validator::make(request()->all(), [
            'id'            => 'required|integer|exists:assessments,id,deleted_at,NULL',
            'course_id'     => 'required|integer|exists:courses,id,deleted_at,NULL',
            'type'          => 'required|in:Exam,Quiz,Assignment',
            'description'   => 'required|string|max:254',
            'percentage'    => 'required|decimal|max:100|min:1',
            'due_date'      => 'required|date|after:today',
        ]);
    }
    
    function delete($id)
    {   request()->merge(['id'=>$id]);
        return Validator::make(request()->all(), [
            'id'            => 'required|integer|exists:assessments,id,deleted_at,NULL',
        ]);
    }

}

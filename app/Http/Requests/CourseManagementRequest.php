<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CourseManagementRequest extends FormRequest
{
    
    function indexStudentCourses($student_id){
        request()->merge(['student_id'=>$student_id]);
        return Validator::make(request()->all(), [
            'student_id'     => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,3|exists:enrollments,student_id',
           
        ]);
    }
    function indexStudentCoursesWithAssessments($student_id){
        request()->merge(['student_id'=>$student_id]);
        return Validator::make(request()->all(), [
            'student_id'     => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,3|exists:enrollments,student_id',
            'course_id'      => 'required|integer|exists:courses,id,deleted_at,NULL'
        ]);
    }
    function indexInstructorCourseWithAssessments($instructor_id){
        request()->merge(['instructor_id'=>$instructor_id]);
        return Validator::make(request()->all(), [
            'instructor_id'     => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,2|exists:enrollments,student_id',
            'course_id'      => 'required|integer|exists:courses,id,deleted_at,NULL'
        ]);
    }
    function indexInstructorCourses($instructor_id){
        request()->merge(['instructor_id'=>$instructor_id]);
        return Validator::make(request()->all(), [
            'instructor_id'     => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,2|exists:courses,instructor_id'
        ]);
    }

    function courseGrades($instructor_id)
    {   request()->merge(['instructor_id'=>$instructor_id]);
        return Validator::make(request()->all(), [
            'instructor_id'     => 'required|integer|exists:users,id,deleted_at,NULL,user_role_id,2|exists:courses,instructor_id',
            'course_id'         => 'required|integer|exists:courses,id,deleted_at,NULL'
         ]);
    }
    
    function store()
    {   
        return Validator::make(request()->all(), [
            'course_name'     => 'required|string|max:255',
            'instructor_id'   => 'required|exists:users,id,user_role_id,2',
            'is_online'       => 'required|boolean',
            'schedule_id'     => 'required|exists:schedules,id,deleted_at,NULL',
        ]);
    }

    function update($id)
    {
        request()->merge(['id'=>$id]);
        return Validator::make(request()->all(), [
            'id'              => 'required|integer|exists:courses,id,deleted_at,NULL',
            'course_name'     => 'required|string|max:255',
            'instructor_id'   => 'required|exists:users,id,user_role_id,2',
            'is_online'       => 'required|boolean',
            'schedule_id'     => 'required|exists:schedules,id',
        ]);
    }
    function delete($id)
    { 
        request()->merge(['id'=>$id]);
        return Validator::make(request()->all(), [
            'id'              => 'required|integer|exists:courses,id,deleted_at,NULL',
        ]);
    }
}

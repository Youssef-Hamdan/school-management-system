<?php

namespace App\Repositories;

use App\Models\Course;
use App\Interface\CourseManagmentInterface;
use App\Interface\CourseManagementInterface;
use App\Models\Enrollment;

class CourseManagementRepository implements CourseManagementInterface
{
    function index(){
        return Course::with(['instructor','schedule'])->get();
    }
    
    function indexInstructorCourses($instructor_id){
        return Course::where('instructor_id', $instructor_id)->get()
                     ->pluck('course_name','id')
                     ->map(function ($course_name,$id) {
                         return ['course_name' => $course_name, 'id' => $id];  
                     });

    }
    function indexStudentCourses($student_id){
        return Course::join('class_courses', 'class_courses.course_id', '=', 'courses.id')
                     ->join('enrollments', 'enrollments.classroom_id', '=', 'class_courses.class_id')
                     ->where('enrollments.student_id',$student_id)
                     ->pluck('courses.course_name','courses.id') 
                     ->map(function ($course_name,$id) {
                        return ['course_name' => $course_name, 'id' => $id];  
                    });
    }
    function indexStudentCoursesWithAssessments($request){
        return Course::with('assessments')->find($request->course_id);
   
    }
    function indexInstructorCourseWithAssessments($request){
        return Course::with('assessments')->find($request->course_id);
   
    }

    function courseGrades($request){
        return Course::join('assessments','assessments.course_id','=','course.id')
                     ->join('grades', 'grades.assessment_id','=','assessments.id')
                     ->join('users as instructors', 'courses.instructor_id', '=', 'instructors.id')
                     ->where('instructor_id', $request->instructor_id)
                     ->select( 
                                'courses.course_id',
                                'courses.course_name',
                                'assessments.type as assessment_name',
                                'assessment.percentage',
                                'grades.student_id',
                                'grades.grade',
                            )
                     ->orderBy('courses.id')
                     ->get();
    }

    function store($request){
        return Course::create([
            'course_name'   => $request->course_name,
            'instructor_id' => $request->instructor_id,
            'is_online'     => $request->is_online,
            'schedule_id'   => $request->schedule_id,
        ]);
    }

    function update($request){
        $course = Course::find($request->id);

        $course->course_name     = $request->course_name;
        $course->instructor_id  = $request->instructor_id;
        $course->is_online      = $request->is_online;
        $course->schedule_id    = $request->schedule_id;
        $course->save();

        return $course;
    }

    function delete($request){
        $course  = Course::find($request->id);
        $course->delete();

        return $course;
    }
}

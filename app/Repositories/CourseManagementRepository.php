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
        return Course::join('assessments', 'assessments.course_id', '=', 'courses.id')
                     ->join('grades', 'grades.assessment_id', '=', 'assessments.id')
                     ->join('users as instructors', 'courses.instructor_id', '=', 'instructors.id')
                     ->join('users as students', 'grades.student_id', '=', 'students.id') // Join with students table
                     ->where([
                         'courses.instructor_id' => $request->instructor_id,
                         'courses.id' => $request->course_id
                     ])
                     ->select(
                         'assessments.id as assessment_id',
                         'courses.id as course_id',
                         'courses.course_name',
                         'assessments.type as assessment_name',
                         'assessments.percentage',
                         'students.id as student_id',
                         'students.first_name as student_first_name',
                         'students.last_name as student_last_name',
                         'grades.id',
                         'grades.grade'
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

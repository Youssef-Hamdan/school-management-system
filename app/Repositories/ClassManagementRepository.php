<?php

namespace App\Repositories;

use App\Models\Classroom;
use App\Models\Enrollment;
use App\Interface\ClassManagementInterface;
use App\Models\ClassCourse;

class ClassManagementRepository implements ClassManagementInterface
{
    function index(){
        return Classroom::join('class_courses', 'classrooms.id', '=', 'class_courses.class_id')
                        ->join('courses', 'class_courses.course_id', '=', 'courses.id')
                        ->select('classrooms.*', 'courses.course_name')
                        ->get();
    } 
    
    function getEnrolledStudents($class_id){
        return Enrollment::where('classroom_id',$class_id)->get()->pluck('student_id');
    } 

    function store($request){
        $classroom = Classroom::create([
            'class_grade'    => $request->class_grade,
            'section_number' => $request->section_number,
            'capacity'       => $request->capacity,
            'location'       => $request->location,
        ]);

        return $classroom;
    }

    function addCourseToClass($request){
        return ClassCourse::create([
            'class_id'      => $request->class_id,
            'course_id'     => $request->course_id,
        ]);
    }
    
    function enroll($request){
        return Enrollment::create([
            'classroom_id'      => $request->classroom_id,
            'student_id'        => $request->student_id,
        ]);
    }

    function update($request){
        $classroom                  = Classroom::find($request->id);

        $classroom->class_grade     = $request->class_grade;
        $classroom->section_number  = $request->section_number;
        $classroom->capacity        = $request->capacity;
        $classroom->location        = $request->location;
        $classroom->save();

        return $classroom;
    }

    function delete($request){
        $classroom = Classroom::find($request->id);
        $classroom->delete();

        return $classroom;
    }
}

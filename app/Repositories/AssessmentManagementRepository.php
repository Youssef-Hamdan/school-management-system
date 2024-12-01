<?php

namespace App\Repositories;

use App\Interface\AssessmentManagementInterface;
use App\Models\Assessment;

class AssessmentManagementRepository implements AssessmentManagementInterface
{
    function indexSudentAssessment($request){
        return Assessment::join('courses', 'courses.id','=', 'assessments.course_id')
                         ->join('class_courses', 'class_courses.course_id','=','courses.id')
                         ->join('enrollments', 'class_courses.class_id','=' ,'enrollments.classroom_id',)
                         ->where('enrollments.student_id', $request->student_id)
                         ->select('enrollments.student_id as student_id', 'courses.course_name as course_name', 'assessments.type as assessment', 'assessments.due_date as due_date')
                         ->orderBy('assessments.due_date')->get();
    }

    function indexInstructorAssessment($request){
        return Assessment::join('courses', 'courses.id','=', 'assessments.course_id')
                         ->where('courses.instructor_id', $request->instructor_id)
                         ->select('courses.instructor_id as instructor_id', 'courses.course_name as course_name', 'assessments.type as assessment', 'assessments.due_date as due_date')
                         ->orderBy('assessments.due_date')->get();
    }

    function store($request){
        return Assessment::create([
            'course_id'     => $request->course_id,
            'type'          => $request->type,
            'description'   => $request->description,
            'percentage'    => $request->percentage,
            'due_date'      => $request->due_date
        ]);
    }

    function update($request){
        $assessment                 = Assessment::find($request->id);
        $assessment->course_id      = $request->course_id;
        $assessment->type           = $request->type;
        $assessment->description    = $request->description;
        $assessment->percentage     = $request->percentage;
        $assessment->due_date       = $request->due_date;
        $assessment->save();

        return $assessment;
    }

    function delete($request){
        $assessment = Assessment::find($request->id);
        $assessment->delete();

        return $assessment;
    }
}

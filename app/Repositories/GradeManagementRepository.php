<?php

namespace App\Repositories;

use App\Models\Grade;
use App\Interface\GradeManagementInterface;

class GradeManagementRepository implements GradeManagementInterface
{
    function index($request){
        return Grade::with(['assessment'])->where('student_id', $request->student_id)->get();
    }

    function store($request){
        return Grade::create([
            'assessment_id'     => $request->assessment_id,
            'student_id'        => $request->student_id,
            'grade'             => $request->grade,
            'is_done'           => false,
        ]);
    }

    function update($request){
        $grade = Grade::find( $request->id);
        $grade->grade = $request->grade;
        $grade->save();

        return $grade;
    }

    function isSubmit($request)
    {
        $grade = Grade::find( $request->id);
        $grade->is_done = $request->is_done;

        return $grade->is_done;
    }

    function delete($request){
        $grade = Grade::find( $request->id);
        $grade->delete();

        return $grade;
    }

}

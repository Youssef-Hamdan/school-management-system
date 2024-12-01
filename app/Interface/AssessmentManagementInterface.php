<?php

namespace App\Interface;

interface AssessmentManagementInterface
{
    function indexSudentAssessment($request);
    function indexInstructorAssessment($request);
    function store($request);
    function update($request);
    function delete($request);

}

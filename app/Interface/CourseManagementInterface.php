<?php

namespace App\Interface;

interface CourseManagementInterface
{
    function index();
    function indexInstructorCourses($instructor_id);
    function indexStudentCourses($student_id);
    function indexStudentCoursesWithAssessments($request);
    function indexInstructorCourseWithAssessments($request);
    function courseGrades($request);
    function store($request);
    function update($request);
    function delete($request);

}

<?php

namespace App\Interface;

interface ClassManagementInterface
{
    function index();
    function getEnrolledStudents($class_id);
    function store($request);
    function addCourseToClass($request);
    function enroll($request);
    function update($request);
    function delete($request);
}

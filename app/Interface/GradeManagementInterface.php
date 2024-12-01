<?php

namespace App\Interface;

Interface GradeManagementInterface
{
    function index($request);
    function store($request);
    function update($request);
    function isSubmit($request);
    function delete($request);
}

<?php

namespace App\Interface;

interface ScheduleManagementInterface
{
    function index();
    function store($request);
    function update($request);
    function delete($request);
}

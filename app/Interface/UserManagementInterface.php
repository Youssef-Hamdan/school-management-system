<?php

namespace App\Interface;

interface UserManagementInterface
{
    function getUser($request);
    function getUsersByRole($request);
    function getUsersBySearch($request);
    function updateUserInfo($request);
    function updateUserStatus($request);
    function usersChart($request);
}

<?php

namespace App\Http\Controllers\User\Instructor;

use Illuminate\Routing\Controller;
use App\Interface\ClassManagementInterface;
use App\Http\Requests\ClassManagementRequest;

class ClassManagementController extends Controller
{
    private $classManagmentRepo, $classManagmentRequest;
    public function __construct(ClassManagementInterface $classManagmentRepo, ClassManagementRequest $classManagmentRequest)
    {
        $this->middleware('auth:api');
        $this->classManagmentRepo = $classManagmentRepo;
        $this->classManagmentRequest = $classManagmentRequest;
    }

    
}

<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthenticationController                     as AdminAuthController;
use App\Http\Controllers\Admin\ClassManagementController                    as AdminClassManagementController;
use App\Http\Controllers\Admin\CourseManagementController                   as AdminCourseManagementController;
use App\Http\Controllers\Admin\UserManagementController                     as AdminUserManagementController;
use App\Http\Controllers\Admin\ScheduleManagementController                 as AdminScheduleManagementController;  

use App\Http\Controllers\User\AuthenticationController                      as UserAuthController;
use App\Http\Controllers\User\UserManagementController                      as UserManagementController;
use App\Http\Controllers\User\Instructor\AssessmentManagementController     as InstructorAssessmentManagementController;  
use App\Http\Controllers\User\Student\AssessmentManagementController        as StudentAssessmentManagementController;  
use App\Http\Controllers\User\Instructor\GradeManagementController          as InstructorGradeManagementController;  
use App\Http\Controllers\User\Student\GradeManagementController             as StudentGradeManagementController;  
use App\Http\Controllers\User\Student\ClassManagementController             as StudentClassManagementController;  
use App\Http\Controllers\User\Instructor\CourseManagementController         as InstructorCourseManagementController;
use App\Http\Controllers\User\Student\CourseManagementController            as StudentCourseManagementController;


Route::group(
    [
        'prefix' => 'admin',
    ],
    function () {
        Route::post('/login',                   [AdminAuthController::class, 'login']);
        Route::post('/registration',            [AdminAuthController::class, 'registration']);
    }
);

Route::group(
    [
        'prefix' => 'user',
    ],
    function () {
        Route::post('/login',                   [UserAuthController::class, 'login']);
        Route::post('/registration',            [UserAuthController::class, 'registration']);
    }
);

// Protected routes (apply jwt.auth middleware)
Route::middleware('jwt.auth')->group(function () {
    Route::group(
        [
            'prefix' => 'admin',
        ],
        function () {
            Route::post('/logout',                           [AdminAuthController::class, 'logout']);
            
            Route::get('/self',                              [AdminUserManagementController::class, 'getSelf']);
            Route::get('/users-role/{user_role_id}',         [AdminUserManagementController::class, 'getUsersByRole']);
            Route::get('/users',                             [AdminUserManagementController::class, 'getUsersBySearch']);
            Route::get('/user/{id}',                         [AdminUserManagementController::class, 'getUser']);
            Route::get('/user-chart',                        [AdminUserManagementController::class, 'usersChart']);
            Route::put('/self-update',                       [AdminUserManagementController::class, 'updateSelfInfo']);
            Route::put('/user-status/{id}',                  [AdminUserManagementController::class, 'updateUserStatus']);

            Route::get('/class',                             [AdminClassManagementController::class, 'index']);
            Route::post('/class',                            [AdminClassManagementController::class, 'create']);
            Route::post('/class-course',                     [AdminClassManagementController::class, 'addCourseToClass']);
            Route::put('/class/{id}',                        [AdminClassManagementController::class, 'update']);
            Route::delete('/class/{id}',                     [AdminClassManagementController::class, 'delete']);

            Route::get('/courses',                           [AdminCourseManagementController::class, 'index']);
            Route::post('/course',                           [AdminCourseManagementController::class, 'create']);
            Route::put('/course/{id}',                       [AdminCourseManagementController::class, 'update']);
            Route::delete('/course/{id}',                    [AdminCourseManagementController::class, 'delete']);

            Route::get('/schedules',                         [AdminScheduleManagementController::class, 'index']);
            Route::post('/schedule',                         [AdminScheduleManagementController::class, 'create']);
            Route::put('/schedule/{id}',                     [AdminScheduleManagementController::class, 'update']);
            Route::delete('/schedule/{id}',                  [AdminScheduleManagementController::class, 'delete']);

            

        }
    );
});

Route::middleware('jwt.auth')->group(function () {
    Route::group(
        [
            'prefix' => 'user',
        ],
        function () {

            // --- Instructor/ Student APIs
            Route::get('/self',                              [UserManagementController::class, 'getSelf']);
            Route::post('/logout',                           [UserAuthController::class, 'logout']);
            Route::put('/update-self',                       [UserManagementController::class, 'updateSelfProfile']);
    
            // --- Student APIs  
            Route::get('/student-assessments',               [StudentAssessmentManagementController::class, 'indexSudentAssessment']);
            Route::get('/student-course-assessments',        [StudentCourseManagementController::class, 'indexStudentCourseWithAssessments']);
            Route::get('/student-grades',                    [StudentGradeManagementController::class, 'index']);
            Route::get('/student-courses',                   [StudentCourseManagementController::class, 'indexStudentCourses']);
            Route::post('/student-classroom-enroll',         [StudentClassManagementController::class, 'enroll']);
            Route::put('/submit',                            [StudentGradeManagementController::class, 'submit']);
        
            // --- Instrutor APIs    
            Route::get('/instructor-assessments',            [InstructorAssessmentManagementController::class, 'indexInstructorAssessment']);
            Route::get('/instructor-course-assessments',     [InstructorCourseManagementController::class, 'indexInstructorCourseWithAssessments']);
            Route::get('/instructor-course-grades',          [InstructorCourseManagementController::class, 'indexCourseGrades']);
            Route::get('/instructor-courses',                [InstructorCourseManagementController::class, 'indexInstructorCourses']);
            Route::post('/assessment',                       [InstructorAssessmentManagementController::class, 'create']);
            Route::post('/grade',                            [InstructorGradeManagementController::class, 'create']);
            Route::put('/assessment/{id}',                   [InstructorAssessmentManagementController::class, 'update']);
            Route::put('/grade/{id}',                        [InstructorGradeManagementController::class, 'update']);
            Route::delete('/assessment/{id}',                [InstructorAssessmentManagementController::class, 'delete']);
            Route::delete('/grade/{id}',                     [InstructorGradeManagementController::class, 'delete']);



        }
    );
});

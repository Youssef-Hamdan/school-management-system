<?php

namespace App\Http\Controllers\User\Instructor;

use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Interface\CourseManagementInterface;
use App\Http\Requests\CourseManagementRequest;

class CourseManagementController extends Controller
{
    private $courseManagmentRepo, $courseManagmentRequest;
    public function __construct(CourseManagementInterface $courseManagmentRepo, CourseManagementRequest $courseManagmentRequest)
    {
        $this->middleware('auth:api');
        $this->courseManagmentRepo = $courseManagmentRepo;
        $this->courseManagmentRequest = $courseManagmentRequest;
    }
    /**
     * @OA\Get(
     *     path="/user/instructor-courses",
     *     tags={"Course Management"},
     *     summary="List Courses for an Instructor",
     *     description="Retrieve a list of courses an instructor is teaching.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of courses",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Instructor not found"
     *     )
     * )
     */

    function indexInstructorCourses(){
        try {
            // Validation
            $instructor_id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->courseManagmentRequest->indexInstructorCourses($instructor_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $courses = $this->courseManagmentRepo->indexInstructorCourses($validated_payload->instructor_id);
            return response()->json(['courses' => $courses], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve assessments', 'message' => $e->getMessage()], 500);
        } 
    }
    /**
     * @OA\Get(
     *     path="/user/instructor-course-grades",
     *     tags={"Course Management"},
     *     summary="Get Course Grades",
     *     description="Retrieve grades for courses taught by a specific instructor.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=true,
     *         description="ID of the course",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of grades",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Instructor not found"
     *     )
     * )
     */

    function indexCourseGrades(){
        try {
            // Validation
            $instructor_id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->courseManagmentRequest->courseGrades($instructor_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $courses = $this->courseManagmentRepo->courseGrades($validated_payload);
            return response()->json(['courses' => $courses], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve assessments', 'message' => $e->getMessage()], 500);
        } 
    }
    /**
     * @OA\Get(
     *     path="/user/instructor-course-assessments",
     *     tags={"Course Management"},
     *     summary="Get Course Grades",
     *     description="Retrieve grades for courses taught by a specific instructor.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=true,
     *         description="ID of the course",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of grades",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Instructor not found"
     *     )
     * )
     */

    function indexInstructorCourseWithAssessments(){
        try {
            // Validation
            $instructor_id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->courseManagmentRequest->indexInstructorCourseWithAssessments($instructor_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $courses = $this->courseManagmentRepo->indexInstructorCourseWithAssessments($validated_payload);
            return response()->json(['courses' => $courses], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve assessments', 'message' => $e->getMessage()], 500);
        } 
    }
}

<?php
namespace App\Http\Controllers\User\Student;

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
     *     path="/user/student-courses",
     *     tags={"Course Management"},
     *     summary="List Courses for a Student",
     *     description="Retrieve a list of courses a student have.",
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

    function indexStudentCourses(){
        try {
            // Validation
            $student_id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->courseManagmentRequest->indexStudentCourses($student_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $courses = $this->courseManagmentRepo->indexStudentCourses($validated_payload->student_id);
            return response()->json(['courses' => $courses], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve assessments', 'message' => $e->getMessage()], 500);
        } 
    }
    /**
     * @OA\Get(
     *     path="/user/student-course-assessments",
     *     tags={"Course Management"},
     *     summary="List Courses for a Student",
     *     description="Retrieve a list of courses a student have.",
     *     security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=true,
     *         description="ID of the course",
     *         @OA\Schema(type="integer", example=2)
     *     ),
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

    function indexStudentCourseWithAssessments(){
        try {
            // Validation
            $student_id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->courseManagmentRequest->indexStudentCoursesWithAssessments($student_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $courses = $this->courseManagmentRepo->indexStudentCoursesWithAssessments($validated_payload);
            return response()->json(['courses' => $courses], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve assessments', 'message' => $e->getMessage()], 500);
        } 
    }


}

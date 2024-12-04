<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\Interface\CourseManagmentInterface;
use App\Interface\CourseManagementInterface;
use App\Http\Requests\CourseManagmentRequest;
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
     *  @OA\Get(
     *  path="/admin/courses",
     *  tags={"Course Management"},
     *  summary="Get All Cpurses",
     *  security={{"bearerAuth":{}}},
     *  description="Retrieve all courses",
     *     @OA\Response(
     *         response=201,
     *         description="Courses returned successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *  @OA\Response(
     *      response=404,
     *      description="User not found"
     *  )
     * )
     */
    public function index()
    {
        try {
            $courses = $this->courseManagmentRepo->index();
            return response()->json(['courses' => $courses], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve courses', 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/admin/course",
     *     tags={"Course Management"},
     *     summary="Create a New Course",
     *     security={{"bearerAuth":{}}},
     *     description="Add a new course to the system.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"course_name", "instructor_id", "is_online", "schedule_id"},
     *             @OA\Property(property="course_name", type="string", maxLength=255, example="Mathematics", description="Name of the course"),
     *             @OA\Property(property="instructor_id", type="integer", example=2, description="ID of the instructor teaching the course"),
     *             @OA\Property(property="is_online", type="boolean", example=true, description="Whether the course is online or in-person"),
     *             @OA\Property(property="schedule_id", type="integer", example=1, description="ID of the schedule associated with the course")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Course created successfully",
     *         @OA\JsonContent(type="object", example={"message": "Course created successfully"})
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    public function create()
    {
        try {
             // Validation
             $validator = $this->courseManagmentRequest->store();
             if ($validator->fails())
                 return response()->json($validator->errors()->first());
 
             $validated_payload = (object) $validator->validated();

             $course = $this->courseManagmentRepo->store($validated_payload);
 
            return response()->json(['message' => 'Course created successfully', 'course' => $course], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create course', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/admin/course/{id}",
     *     tags={"Course Management"},
     *     summary="Update a Course",
     *     security={{"bearerAuth":{}}},
     *     description="Update details of an existing course.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the course to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"course_name", "instructor_id", "is_online", "schedule_id"},
     *             @OA\Property(property="course_name", type="string", maxLength=255, example="Advanced Mathematics", description="Name of the course"),
     *             @OA\Property(property="instructor_id", type="integer", example=2, description="ID of the instructor teaching the course"),
     *             @OA\Property(property="is_online", type="boolean", example=false, description="Whether the course is online or in-person"),
     *             @OA\Property(property="schedule_id", type="integer", example=2, description="ID of the schedule associated with the course")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course updated successfully",
     *         @OA\JsonContent(type="object", example={"message": "Course updated successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found"
     *     )
     * )
     */

    public function update($id)
    {
        try {
             // Validation
             $validator = $this->courseManagmentRequest->update($id);
             if ($validator->fails())
                 return response()->json($validator->errors()->first());
 
             $validated_payload = (object) $validator->validated();

             $course = $this->courseManagmentRepo->update($validated_payload);
 
            return response()->json(['message' => 'Course updated successfully', 'course' => $course], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update course', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/admin/course/{id}",
     *     tags={"Course Management"},
     *     summary="Delete a Course",
     *     security={{"bearerAuth":{}}},
     *     description="Delete an existing course.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the course to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course deleted successfully",
     *         @OA\JsonContent(type="object", example={"message": "Course deleted successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found"
     *     )
     * )
     */

    public function delete($id)
    {
        try {
            // Validation
            $validator = $this->courseManagmentRequest->delete($id);
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();

            $course = $this->courseManagmentRepo->delete($validated_payload);

            return response()->json(['message' => 'Course deleted successfully','course' => $course], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete course', 'message' => $e->getMessage()], 500);
        }
    }
}

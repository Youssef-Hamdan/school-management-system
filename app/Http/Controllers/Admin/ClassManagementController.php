<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\ClassCourse;
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

    /**
     *  @OA\Get(
     *  path="/admin/classes",
     *  tags={"Class Management"},
     *  summary="Get All Classes",
     *  security={{"bearerAuth":{}}},
     *  description="Retrieve all classes",
     *     @OA\Response(
     *         response=201,
     *         description="Class returned successfully",
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
            $classes = $this->classManagmentRepo->index();
            return response()->json(['classes' => $classes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve classes', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/admin/class",
     *     tags={"Class Management"},
     *     summary="Create a new Class",
     *     security={{"bearerAuth":{}}},
     *     description="Add a new class to the system.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"class_grade", "section_number", "capacity", "location"},
     *             @OA\Property(property="class_grade", type="string", maxLength=255, example="Grade 10", description="Grade level of the class"),
     *             @OA\Property(property="section_number", type="string", maxLength=255, example="A", description="Section number of the class"),
     *             @OA\Property(property="capacity", type="integer", example=30, description="Maximum capacity of the class"),
     *             @OA\Property(property="location", type="string", maxLength=255, example="Room 101", description="Location of the class")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Class created successfully",
     *         @OA\JsonContent(type="object", example={"message": "Class created successfully"})
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
            $validator = $this->classManagmentRequest->store();
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();

            $classroom = $this->classManagmentRepo->store($validated_payload);

            return response()->json(['message' => 'Class created successfully', 'classroom' => $classroom], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create class', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/admin/class-course",
     *     tags={"Class Management"},
     *     summary="Add a course to a Class",
     *     security={{"bearerAuth":{}}},
     *     description="Add a course to a class.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"class_id", "course_id"},
     *             @OA\Property(property="class_id", type="integer",  example="1", description="Class id that you want to add the course to."),
     *             @OA\Property(property="course_id", type="integer", example="1", description="The added course."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Class created successfully",
     *         @OA\JsonContent(type="object", example={"message": "Class created successfully"})
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
    */

    public function addCourseToClass(){
        
        try {
            // Validation
            $validator = $this->classManagmentRequest->addCourseToClass();
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();
            $course = Course::find($validated_payload->course_id);

            if (!$course) {
                return response()->json(['error' => 'Course not found'], 404);
            }
            
            $courses = ClassCourse::where('class_id',$validated_payload->class_id)->get()->pluck('course_id');
            $has_conflict = Course::whereIn('id',$courses)->where('schedule_id', $course->schedule_id)->exists();
            
            if ($has_conflict) {
                return response()->json(['error' => 'Time Conflict: Another course already exists for the same schedule.'], 409);
            }
        
            $classroom = $this->classManagmentRepo->addCourseToClass($validated_payload);

            return response()->json(['message' => 'Course added to Class successfully', 'classroom' => $classroom], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create class', 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * @OA\Put(
     *     path="/admin/class/{id}",
     *     tags={"Class Management"},
     *     summary="Update an Existing Class",
     *     security={{"bearerAuth":{}}},
     *     description="Update the details of an existing class.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the class to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"class_grade", "section_number", "capacity", "location"},
     *             @OA\Property(property="class_grade", type="string", maxLength=255, example="Grade 11", description="Grade level of the class"),
     *             @OA\Property(property="section_number", type="string", maxLength=255, example="B", description="Section number of the class"),
     *             @OA\Property(property="capacity", type="integer", example=25, description="Maximum capacity of the class"),
     *             @OA\Property(property="location", type="string", maxLength=255, example="Room 202", description="Location of the class")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Class updated successfully",
     *         @OA\JsonContent(type="object", example={"message": "Class updated successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Class not found"
     *     )
     * )
     */

    public function update($id)
    {
        try {
            // Validation
            $validator = $this->classManagmentRequest->update($id);
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();
            
            $classroom = $this->classManagmentRepo->update($validated_payload);

            return response()->json(['message' => 'Class updated successfully', 'classroom' => $classroom], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update class', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/admin/class/{id}",
     *     tags={"Class Management"},
     *     summary="Delete a Class",
     *     security={{"bearerAuth":{}}},
     *     description="Delete an existing class.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the class to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Class deleted successfully",
     *         @OA\JsonContent(type="object", example={"message": "Class deleted successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Class not found"
     *     )
     * )
     */

    public function delete($id)
    {
        try{
            // Validation
            $validator = $this->classManagmentRequest->delete($id);
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();
            
            $classroom = $this->classManagmentRepo->delete($validated_payload);

            return response()->json(['message' => 'Class deleted successfully','classroom' => $classroom], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete class', 'message' => $e->getMessage()], 500);
        }
    }
}

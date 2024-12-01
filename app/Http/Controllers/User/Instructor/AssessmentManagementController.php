<?php

namespace App\Http\Controllers\User\Instructor;

use App\Models\Grade;
use App\Models\ClassCourse;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Interface\ClassManagementInterface;
use App\Interface\GradeManagementInterface;
use App\Interface\AssessmentManagementInterface;
use App\Http\Requests\AssessmentManagementRequest;

class AssessmentManagementController extends Controller
{
    private $assessmentManagementRepo, $classManagementRepo, $gradeManagementRepo, $assessmentManagementRequest;

    public function __construct(AssessmentManagementInterface $assessmentManagementRepo, ClassManagementInterface $classManagementRepo, GradeManagementInterface $gradeManagementRepo, AssessmentManagementRequest $assessmentManagementRequest) {
        
        $this->middleware('auth:api');
        $this->assessmentManagementRepo = $assessmentManagementRepo;
        $this->assessmentManagementRequest = $assessmentManagementRequest;
        $this->classManagementRepo = $classManagementRepo;
        $this->gradeManagementRepo = $gradeManagementRepo;
    }

    /**
     * @OA\Get(
     *     path="/user/instructor-assessments",
     *     tags={"Assessment Management"},
     *     summary="Get Assessments for an Instructor",
     *     description="Retrieve assessments created by a specific instructor.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of assessments",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Instructor not found"
     *     )
     * )
     */

    public function indexInstructorAssessment()
    {
        try {
            // Validation
            $instructor_id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->assessmentManagementRequest->indexInstructorAssessment($instructor_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $assessments = $this->assessmentManagementRepo->indexInstructorAssessment($validated_payload);
            return response()->json(['assessments' => $assessments], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve assessments', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/assessment",
     *     tags={"Assessment Management"},
     *     summary="Create a new Assessment",
     *     description="Add a new assessment to a course.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"course_id", "type", "description", "percentage", "due_date"},
     *             @OA\Property(property="course_id", type="integer", example=1, description="ID of the course"),
     *             @OA\Property(property="type", type="string", enum={"Exam", "Quiz", "Assignment"}, example="Exam", description="Type of assessment"),
     *             @OA\Property(property="description", type="string", maxLength=254, example="Midterm Exam", description="Description of the assessment"),
     *             @OA\Property(property="percentage", type="number", format="decimal", example=25.5, description="Percentage weight of the assessment"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2024-12-31", description="Due date of the assessment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Assessment created successfully",
     *         @OA\JsonContent(type="object", example={"message": "Assessment created successfully"})
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
            $instructor_id = JWTAuth::parseToken()->authenticate()->id;
            $validator    = $this->assessmentManagementRequest->store($instructor_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $course_class_id = ClassCourse::where('course_id',$validated_payload->course_id)->first()->class_id;
            $has_conflict = ClassCourse::join('assessments','assessments.course_id','=','class_courses.course_id')
                                                    ->where('class_courses.class_id',$course_class_id)
                                                    ->where('assessments.due_date',$validated_payload->due_date)->exists();
                                                    
            if($has_conflict)
                return response()->json(['error' => 'Time Conflict'], 404);

            // Create the assessment
            $assessment = $this->assessmentManagementRepo->store($validated_payload);

            // Get class_id for the course
            $class_course = ClassCourse::where('course_id', $validated_payload->course_id)->first();
            if (!$class_course) {
                return response()->json(['error' => 'Class not found for the course'], 404);
            }

            $class_id = $class_course->class_id;

           
            // Get enrolled students for the class
            $students_ids = $this->classManagementRepo->getEnrolledStudents($class_id);
            if ($students_ids->isEmpty()) {
                return response()->json(['message' => 'No students enrolled in this class'], 200);
            }

            // Prepare grades for batch creation
            $grades = [];
            foreach ($students_ids as $student_id) {
                $grades[] = [
                    'student_id' => $student_id,
                    'assessment_id' => $assessment->id,
                    'grade' => 0, 
                    'is_done' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Create all grades at once
            Grade::insert($grades);

            return response()->json(['message' => 'Assessment created successfully', 'assessment' => $assessment], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create assessment', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/user/assessment/{id}",
     *     tags={"Assessment Management"},
     *     summary="Update an Assessment",
     *     description="Update the details of an existing assessment.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the assessment",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"course_id", "type", "description", "percentage", "due_date"},
     *             @OA\Property(property="course_id", type="integer", example=1, description="ID of the course"),
     *             @OA\Property(property="type", type="string", enum={"Exam", "Quiz", "Assignment"}, example="Quiz", description="Type of assessment"),
     *             @OA\Property(property="description", type="string", maxLength=254, example="Pop Quiz", description="Description of the assessment"),
     *             @OA\Property(property="percentage", type="number", format="decimal", example=15.0, description="Percentage weight of the assessment"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2024-11-30", description="Due date of the assessment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment updated successfully",
     *         @OA\JsonContent(type="object", example={"message": "Assessment updated successfully"})
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    public function update($id)
    {
        try {
            // Validation
            $validator = $this->assessmentManagementRequest->update($id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $assessment = $this->assessmentManagementRepo->update($validated_payload);

            return response()->json(['message' => 'Assessment updated successfully', 'assessment' => $assessment], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update assessment', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/user/assessment/{id}",
     *     tags={"Assessment Management"},
     *     summary="Delete an Assessment",
     *     description="Delete an existing assessment.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the assessment to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment deleted successfully",
     *         @OA\JsonContent(type="object", example={"message": "Assessment deleted successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assessment not found"
     *     )
     * )
     */

    public function delete($id)
    {
        try {
            // Validation
            $validator = $this->assessmentManagementRequest->delete($id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }
            $validated_payload = (object) $validator->validated();

            $deleted = $this->assessmentManagementRepo->delete($validated_payload);

            return response()->json(['message' => 'Assessment deleted successfully', 'deleted' => $deleted], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete assessment', 'message' => $e->getMessage()], 500);
        }
    }
}

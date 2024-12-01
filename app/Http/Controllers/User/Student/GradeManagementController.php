<?php
namespace App\Http\Controllers\User\Student;

use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Interface\GradeManagementInterface;
use App\Http\Requests\GradeManagementRequest;

class GradeManagementController extends Controller
{
    private $gradeManagementRepo, $gradeManagementRequest;

    public function __construct( GradeManagementInterface $gradeManagementRepo, GradeManagementRequest $gradeManagementRequest) {
        $this->middleware('auth:api');
        $this->gradeManagementRepo = $gradeManagementRepo;
        $this->gradeManagementRequest = $gradeManagementRequest;
    }

        // --- retreive student grades
    /**
     * @OA\Get(
     *     path="/user/student-grades",
     *     tags={"Grade Management"},
     *     summary="List Grades for a Student",
     *     description="Retrieve a list of grades for a specific student.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of grades",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */

    public function index()
    {
        try {
            // Validate the request
            $student_id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->gradeManagementRequest->index($student_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }
            $validated_payload = (object) $validator->validated();

            $grades = $this->gradeManagementRepo->index($validated_payload);

            return response()->json(['grades' => $grades], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve grades', 'message' => $e->getMessage()], 500);
        }
    }

    // --- students submit there assessment
    /**
     * @OA\Post(
     *     path="/user/submit",
     *     tags={"Grade Management"},
     *     summary="Mark a Grade as Submitted",
     *     description="Mark a specific grade as submitted or active.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id", "is_active"},
     *             @OA\Property(property="id", type="integer", example=10, description="ID of the grade"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="Submission status")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Submission status updated successfully",
     *         @OA\JsonContent(type="object", example={"message": "Submission status updated successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grade not found"
     *     )
     * )
     */

    public function Submit()
    {
        try {
            // Validate the request
            $validator = $this->gradeManagementRequest->isSubmit();
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $submitted = $this->gradeManagementRepo->isSubmit($validated_payload);

            return response()->json(['message' => 'Grade created successfully', 'submitted' => $submitted], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create grade', 'message' => $e->getMessage()], 500);
        }
    }

}

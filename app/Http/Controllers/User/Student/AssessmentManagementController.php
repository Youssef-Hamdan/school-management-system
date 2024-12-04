<?php

namespace App\Http\Controllers\User\Student;

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
     *     path="/user/student-assessments",
     *     tags={"Assessment Management"},
     *     summary="Get Assessments for a Student",
     *     description="Retrieve assessments associated with a specific student.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of assessments",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */

    public function indexSudentAssessment()
    {
        try {
            // Validation
            $student_id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->assessmentManagementRequest->indexSudentAssessment($student_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $assessments = $this->assessmentManagementRepo->indexSudentAssessment($validated_payload);
            return response()->json(['assessments' => $assessments], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve assessments', 'message' => $e->getMessage()], 500);
        }
    }
   
}

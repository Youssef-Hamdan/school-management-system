<?php
namespace App\Http\Controllers\User\Instructor;

use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Interface\GradeManagementInterface;
use App\Http\Requests\GradeManagementRequest;

class GradeManagementController extends Controller
{
    private $gradeManagementRepo, $gradeManagementRequest;

    public function __construct(
        GradeManagementInterface $gradeManagementRepo,
        GradeManagementRequest $gradeManagementRequest
    ) {
        $this->middleware('auth:api');
        $this->gradeManagementRepo = $gradeManagementRepo;
        $this->gradeManagementRequest = $gradeManagementRequest;
    }

    /**
     * @OA\Post(
     *     path="/user/grade",
     *     tags={"Grade Management"},
     *     summary="Store a New Grade",
     *     description="Add a new grade for a student in a specific assessment.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"assessment_id", "student_id", "grade"},
     *             @OA\Property(property="assessment_id", type="integer", example=5, description="ID of the assessment"),
     *             @OA\Property(property="student_id", type="integer", example=1, description="ID of the student"),
     *             @OA\Property(property="grade", type="integer", example=85, description="Grade awarded, must be between 0 and 100")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Grade created successfully",
     *         @OA\JsonContent(type="object", example={"message": "Grade created successfully"})
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
            // Validate the request
            $validator = $this->gradeManagementRequest->store();
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $grade = $this->gradeManagementRepo->store($validated_payload);

            return response()->json(['message' => 'Grade created successfully', 'grade' => $grade], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create grade', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/user/grade/{id}",
     *     tags={"Grade Management"},
     *     summary="Update an Existing Grade",
     *     description="Update the details of an existing grade.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the grade to update",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"grade"},
     *             @OA\Property(property="grade", type="integer", example=90, description="Grade awarded, must be between 0 and 100")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Grade updated successfully",
     *         @OA\JsonContent(type="object", example={"message": "Grade updated successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grade not found"
     *     )
     * )
     */

    public function update($id)
    {
        try {
            // Validate the request
            $instructor_id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->gradeManagementRequest->update($id,$instructor_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $grade = $this->gradeManagementRepo->update($validated_payload);

            return response()->json(['message' => 'Grade updated successfully', 'grade' => $grade], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update grade', 'message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            // Validate the request
            $validator = $this->gradeManagementRequest->delete($id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $grade = $this->gradeManagementRepo->delete($validated_payload);

            return response()->json(['message' => 'Grade deleted successfully', 'grade' => $grade], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete grade', 'message' => $e->getMessage()], 500);
        }
    }
}

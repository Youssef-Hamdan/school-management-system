<?php
namespace App\Http\Controllers\User\Student;

use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
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
     * @OA\Post(    
     *     path="/user/student-classroom-enroll",
     *     tags={"Class Management"},
     *     summary="Enroll a Student in a Class",
     *     description="Enroll a specific student in a class.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"classroom_id"},
     *             @OA\Property(property="classroom_id", type="integer", example=1, description="ID of the classroom"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student enrolled successfully",
     *         @OA\JsonContent(type="object", example={"message": "Student enrolled successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Classroom or student not found"
     *     )
     * )
     */

    function enroll(){
        try{
            // Validate the request
            $student_id = JWTAuth::parseToken()->authenticate()->id;
            
            $validator = $this->classManagmentRequest->enroll($student_id);
            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 400);
            }

            $validated_payload = (object) $validator->validated();

            $enroll = $this->classManagmentRepo->enroll($validated_payload);

            return response()->json(['message' => 'Enrolled successfully', 'enrolled' => $enroll], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create grade', 'message' => $e->getMessage()], 500);
        } 
    }
}

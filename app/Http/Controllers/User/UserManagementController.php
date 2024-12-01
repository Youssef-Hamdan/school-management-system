<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Interface\UserManagementInterface;
use App\Http\Requests\UserManagementRequest;

class UserManagementController extends Controller
{
    private $userManagmentRepo, $userManagmentRequest;
    public function __construct(UserManagementInterface $userManagmentRepo, UserManagementRequest $userManagmentRequest)
    {
        $this->middleware('auth:api');
        $this->userManagmentRepo = $userManagmentRepo;
        $this->userManagmentRequest = $userManagmentRequest;
    }

/**
 * @OA\Put(
 *     path="/user/update-self/{id}",
 *     tags={"User Management"},
 *     summary="Update User Information",
 *     description="Update the details of an existing user.",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "date_of_birth", "email", "password"},
 *             @OA\Property(property="first_name", type="string", maxLength=30, example="Jane"),
 *             @OA\Property(property="last_name", type="string", maxLength=30, example="Smith"),
 *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
 *             @OA\Property(property="profile_image", type="string", format="binary", description="Profile image of the user"),
 *             @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
 *             @OA\Property(property="password", type="string", example="SecurePassword123!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(type="object", example={"message": "User updated successfully"})
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */

    function updateSelfProfile(){
        try {
            $id = JWTAuth::parseToken()->authenticate()->id;
            $validator = $this->userManagmentRequest->updateUserInfo($id);
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();

            $user = $this->userManagmentRepo->updateUserInfo($validated_payload);

            return response()->json(['message' => 'User information updated successfully', 'user' => $user], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update user info', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        
    /**
     * @OA\Get(
     *     path="/user/self",
     *     summary="Get the authenticated user's information",
     *     description="This endpoint returns the details of the currently authenticated user.",
     *     operationId="getSelf",
     *     tags={"User Management"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved user information",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=10),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-11-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized — No valid authentication token was provided",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found — User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function getSelf()
    {
        return User::where('id', JWTAuth::parseToken()->authenticate()->id)->first();
    }

}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Interface\UserManagmentInterface;
use App\Interface\UserManagementInterface;
use App\Http\Requests\UserManagmentRequest;
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

     // --- retreive user by id and role
    /**
     *  @OA\Get(
     *  path="/admin/user/{id}",
     *  tags={"User Management"},
     *  summary="Get User Information",
     *  security={{"bearerAuth":{}}},
     *  description="Retrieve information of a user by their ID and role.",
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="ID of the user",
     *      @OA\Schema(type="integer", example=1)
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="User information retrieved successfully",
     *      @OA\JsonContent(type="object", example={"id": 1, "first_name": "John", "last_name": "Doe", "email": "john@example.com"})
     *  ),
     *  @OA\Response(
     *      response=404,
     *      description="User not found"
     *  )
     * )
     */

     public function getUser($id)
    {
        try {
            $validator = $this->userManagmentRequest->showUser($id);
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();

            $user = $this->userManagmentRepo->getUser($validated_payload);
            return response()->json(['message' => 'Users fetched successfully', 'user' => $user], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch users', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // --- retreive a chart of users each year
    /**
 * @OA\Get(
 *     path="/admin/user-chart",
 *     tags={"User Management"},
 *     summary="Get Users Chart",
 *     security={{"bearerAuth":{}}},
 *     description="Retrieve user statistics based on role and date range.",
 *     @OA\Parameter(
 *         name="user_role_id",
 *         in="query",
 *         required=true,
 *         description="Role ID of the users",
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\Parameter(
 *         name="start_date",
 *         in="query",
 *         required=true,
 *         description="Start date for the chart",
 *         @OA\Schema(type="string", format="date-time", example="2024-01-01 00:00:00")
 *     ),
 *     @OA\Parameter(
 *         name="end_date",
 *         in="query",
 *         required=true,
 *         description="End date for the chart",
 *         @OA\Schema(type="string", format="date-time", example="2024-12-31 23:59:59")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Chart data retrieved successfully",
 *         @OA\JsonContent(type="array", @OA\Items(type="object"))
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

    public function usersChart()
    {
        try {
            $validator = $this->userManagmentRequest->usersChart();
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();

            $chart = $this->userManagmentRepo->usersChart($validated_payload);
            return response()->json(['message' => 'Users fetched successfully', 'chart' => $chart], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch users', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // --- retreive users by role
    /**
 * @OA\Get(
 *     path="/admin/users-role/{user_role_id}",
 *     tags={"User Management"},
 *     summary="Get Users by Role",
 *     security={{"bearerAuth":{}}},
 *     description="Retrieve a list of users by their role.",
 *     @OA\Parameter(
 *         name="user_role_id",
 *         in="path",
 *         required=true,
 *         description="Role ID",
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Users retrieved successfully",
 *         @OA\JsonContent(type="array", @OA\Items(type="object"))
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found"
 *     )
 * )
 */

    public function getUsersByRole($user_role_id)
    {
        try {
            $validator = $this->userManagmentRequest->getUsersByRole($user_role_id);
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();
            
            $users = $this->userManagmentRepo->getUsersByRole($validated_payload);
            return response()->json(['message' => 'Users fetched successfully', 'users' => $users], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch users', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/admin/users",
     *     tags={"User Management"},
     *     summary="Get Users by Search bar",
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve a list of users by their role search bar.",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="search",
     *         @OA\Schema(type="string", example="youssef")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found"
     *     )
     * )
     */

    public function getUsersBySearch()
    {
        try {
            $validator = $this->userManagmentRequest->getUsersBySearch();
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();
            
            $users = $this->userManagmentRepo->getUsersBySearch($validated_payload);
            return response()->json(['message' => 'Users fetched successfully', 'users' => $users], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch users', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // --- update self admin info
    /**
 * @OA\Put(
 *     path="/admin/self-update",
 *     tags={"User Management"},
 *     summary="Update User Information",
 *     security={{"bearerAuth":{}}},
 *     description="Update the details of an existing user.",
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

    public function updateSelfInfo()
    {
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

    // --- update the status of the user
    /**
     * @OA\Put(
     *     path="/admin/user-status/{id}",
     *     tags={"User Management"},
     *     summary="Update User Status",
     *     security={{"bearerAuth":{}}},
     *     description="Enable or disable a user's status.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"is_active"},
     *             @OA\Property(property="is_active", type="boolean", example=true, description="Status of the user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User status updated successfully",
     *         @OA\JsonContent(type="object", example={"message": "User status updated successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */

    public function updateUserStatus($id)
    {
        try {
            $validator = $this->userManagmentRequest->updateUserStatus($id);
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();
            $user = $this->userManagmentRepo->updateUserStatus($validated_payload);

            return response()->json(['message' => 'User status updated successfully', 'user' => $user], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update user status', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
       /**
     * @OA\Get(
     *     path="/admin/self",
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

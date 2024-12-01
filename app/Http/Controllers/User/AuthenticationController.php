<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use App\Interface\AuthenticationInterface;
use App\Http\Requests\AuthenticationRequest;


/**
 * @OA\Tag(
 *     name="User",
 *     description="Admin API Endpoints"
 * )
 */
class AuthenticationController extends Controller 
{
    private $authenticationRepo, $authenticationRequest;
    public function __construct(AuthenticationInterface $authenticationRepo, AuthenticationRequest $authenticationRequest)
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration']]);
        $this->authenticationRepo = $authenticationRepo;
        $this->authenticationRequest = $authenticationRequest;
    }

// == Auth

    // --- Login
   /**
     * @OA\Post(
     *     path="/user/login",
     *     tags={"Authentication"},
     *     summary="Login for User",
     *     description="Authenticate user and get a JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="Bearer <your-token>")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(AuthenticationRequest $request)
    {
        try{
            //-- validation
            $validator = $this->authenticationRequest->login();
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            if (Auth::check()) Auth::logout();

            $user = User::where('email', $request->email)->first();

            if (!$user) 
                return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            
            
            if (!Hash::check($request->password, $user->password)) 
                return response()->json(['error' => 'Incorrect password'], Response::HTTP_UNAUTHORIZED);
                
            
            $credentials = $request->only('email', 'password');
            $token = JWTAuth::attempt($credentials);
            if (!$token) return response()->json('incorrect_passwordd', Response::HTTP_BAD_REQUEST);
            
            $user = Auth::user();
            $cookie = cookie('jwt', $token, config('jwt.ttl'), null, null, true, true,false,"none"); 
            return response()->json(['message' => __('messages.login'), 'user' => $user, 'token' => $token])->withCookie($cookie);

        } catch(Exception $e){

            return response()->json($e->getMessage()) ;
        }
    }
    

    // --- Registration
    /**
 * @OA\Post(
 *     path="/user/registration",
 *     tags={"Authentication"},
 *     summary="User Registration",
 *     description="Register a new user account.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "date_of_birth", "email", "password", "user_role_id"},
 *             @OA\Property(property="first_name", type="string", maxLength=30, example="John", description="User's first name"),
 *             @OA\Property(property="last_name", type="string", maxLength=30, example="Doe", description="User's last name"),
 *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01", description="User's date of birth"),
 *             @OA\Property(property="profile_image", type="string", format="binary", description="User's profile image (optional)"),
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User's email address"),
 *             @OA\Property(property="password", type="string", format="password", example="Password123!", description="User's password with mixed case, numbers, and symbols"),
 *             @OA\Property(property="user_role_id",type="integer",description="2 (Instructor), 3 (Student)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Registration successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Registration successful"),
 *             @OA\Property(property="user", type="object", description="Details of the registered user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 */

    public function registration(Request $request)
    {
        try {

            // Set portal_id to 2
            $request->merge([
                'portal_id' => 2,
                'is_active' => false,  ]);       

            $validator = $this->authenticationRequest->registration();
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            DB::beginTransaction();
            // Save the user using the repository
            $user = $this->authenticationRepo->store($request);

            DB::commit();
    
            return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => 'Registration failed', 'message' => $ex->getMessage()], 500);
        }
    }
    
    // --- Logout
     /**
     * @OA\Post(
     *     path="/user/logout",
     *     tags={"Authentication"},
     *     summary="Logout for User",
     *     security={{"bearerAuth":{}}},
     *     description="Logout the authenticated user and invalidate the token",
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Logout failed"
     *     )
     * )
     */
    public function logout()
{
    try {
        // Invalidate the JWT token
        JWTAuth::invalidate(JWTAuth::getToken());
        $cookie = Cookie::forget('jwt');

        return response()->json(['message' => 'Logged out successfully'])->withCookie($cookie);
    } catch (Exception $ex) {
        return response()->json(['error' => 'Logout failed', 'message' => $ex->getMessage()], 500);
    }
}


}

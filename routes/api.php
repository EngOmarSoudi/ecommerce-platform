<?php

use App\Http\Controllers\Api\HealthCheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * @OA\Get(
 *     path="/api/health",
 *     summary="Health check endpoint",
 *     tags={"Health"},
 *     @OA\Response(
 *         response=200,
 *         description="Health check successful"
 *     )
 */
Route::get('/health', [HealthCheckController::class, 'index']);

Route::prefix('v1')->group(function () {
    // Public routes
    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="phone", type="string", example="+1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="1|abcdefghijk123456"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    Route::post('/auth/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    
    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="1|abcdefghijk123456"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    Route::post('/auth/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    
    /**
     * @OA\Post(
     *     path="/api/v1/auth/phone/request-otp",
     *     summary="Request OTP for phone authentication",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="+1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP sent successfully"),
     *             @OA\Property(property="phone", type="string", example="+1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to send OTP"
     *     )
     * )
     */
    Route::post('/auth/phone/request-otp', [\App\Http\Controllers\Api\PhoneAuthController::class, 'requestOTP']);
    
    /**
     * @OA\Post(
     *     path="/api/v1/auth/phone/verify-otp",
     *     summary="Verify OTP and login/register user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone","otp"},
     *             @OA\Property(property="phone", type="string", example="+1234567890"),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *             @OA\Property(property="name", type="string", example="John Doe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Authentication successful"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="1|abcdefghijk123456"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid OTP"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    Route::post('/auth/phone/verify-otp', [\App\Http\Controllers\Api\PhoneAuthController::class, 'verifyOTP']);
    
    /**
     * @OA\Post(
     *     path="/api/v1/auth/social/login",
     *     summary="Handle social login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"provider","provider_id","name","email"},
     *             @OA\Property(property="provider", type="string", example="google"),
     *             @OA\Property(property="provider_id", type="string", example="123456789"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="avatar", type="string", example="https://example.com/avatar.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Social login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Social login successful"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="1|abcdefghijk123456"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    Route::post('/auth/social/login', [\App\Http\Controllers\Api\SocialAuthController::class, 'socialLogin']);
    
    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     summary="Get all categories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="List of categories",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     )
     * )
     */
    Route::get('/categories', function () {
        return response()->json([
            'data' => \App\Models\Category::all()
        ]);
    });
    
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Get all products with pagination",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
     *         )
     *     )
     * )
     */
    Route::get('/products', function () {
        $products = \App\Models\Product::with('skus')
            ->paginate(15);
        
        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    });
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        /**
         * @OA\Post(
         *     path="/api/v1/auth/logout",
         *     summary="Logout user",
         *     tags={"Authentication"},
         *     security={{"bearerAuth": {}}},
         *     @OA\Response(
         *         response=200,
         *         description="Logged out successfully",
         *         @OA\JsonContent(
         *             @OA\Property(property="message", type="string", example="Logged out successfully")
         *         )
         *     )
         * )
         */
        Route::post('/auth/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
        
        /**
         * @OA\Get(
         *     path="/api/v1/me",
         *     summary="Get authenticated user",
         *     tags={"User"},
         *     security={{"bearerAuth": {}}},
         *     @OA\Response(
         *         response=200,
         *         description="Authenticated user data",
         *         @OA\JsonContent(
         *             @OA\Property(property="user", ref="#/components/schemas/User")
         *         )
         *     )
         * )
         */
        Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
        
        /**
         * @OA\Put(
         *     path="/api/v1/me",
         *     summary="Update authenticated user",
         *     tags={"User"},
         *     security={{"bearerAuth": {}}},
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             @OA\Property(property="name", type="string", example="John Updated"),
         *             @OA\Property(property="email", type="string", format="email", example="john.updated@example.com"),
         *             @OA\Property(property="phone", type="string", example="+1234567890"),
         *             @OA\Property(property="password", type="string", format="password"),
         *             @OA\Property(property="password_confirmation", type="string", format="password")
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Profile updated successfully",
         *         @OA\JsonContent(
         *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
         *             @OA\Property(property="user", ref="#/components/schemas/User")
         *         )
         *     ),
         *     @OA\Response(
         *         response=422,
         *         description="Validation failed"
         *     )
         * )
         */
        Route::put('/me', [\App\Http\Controllers\Api\AuthController::class, 'update']);
    });
});
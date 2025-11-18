<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OTPServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PhoneAuthController extends Controller
{
    protected OTPServiceInterface $otpService;

    public function __construct(OTPServiceInterface $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Request OTP for phone authentication
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
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
    public function requestOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        $phoneNumber = $request->phone;
        $otp = $this->otpService->generateOTP($phoneNumber);
        
        // In a real implementation, you would store the OTP with an expiration time
        // For now, we'll just send it
        
        if ($this->otpService->sendOTP($phoneNumber, $otp)) {
            return response()->json([
                'message' => 'OTP sent successfully',
                'phone' => $phoneNumber
            ]);
        }

        return response()->json([
            'error' => 'Failed to send OTP'
        ], 500);
    }

    /**
     * Verify OTP and login/register user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
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
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'otp' => 'required|string|size:6',
            'name' => 'nullable|string|max:255', // Only required for registration
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        $phoneNumber = $request->phone;
        $otp = $request->otp;

        if (!$this->otpService->verifyOTP($phoneNumber, $otp)) {
            return response()->json([
                'error' => 'Invalid OTP'
            ], 401);
        }

        // Check if user exists
        $user = User::where('phone', $phoneNumber)->first();

        // If user doesn't exist, create a new one
        if (!$user) {
            $user = User::create([
                'name' => $request->name ?? 'User ' . $phoneNumber,
                'email' => $phoneNumber . '@phone.local', // Generate a dummy email
                'password' => Hash::make($phoneNumber), // Use phone as password
                'phone' => $phoneNumber,
            ]);

            // Assign default role (customer)
            $customerRole = \App\Models\Role::where('name', 'customer')->first();
            if ($customerRole) {
                $user->roles()->attach($customerRole);
            }
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Authentication successful',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SocialAuthController extends Controller
{
    /**
     * Handle social login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
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
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|in:google,facebook,twitter,github',
            'provider_id' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'avatar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        // Check if user exists with this provider ID
        $user = User::where('provider', $request->provider)
            ->where('provider_id', $request->provider_id)
            ->first();

        // If user doesn't exist, create a new one
        if (!$user) {
            // Check if user exists with this email
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make(uniqid()), // Generate a random password
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id,
                    'avatar' => $request->avatar,
                ]);
                
                // Assign default role (customer)
                $customerRole = \App\Models\Role::where('name', 'customer')->first();
                if ($customerRole) {
                    $user->roles()->attach($customerRole);
                }
            } else {
                // Update existing user with provider info
                $user->update([
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id,
                    'avatar' => $request->avatar,
                ]);
            }
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Social login successful',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
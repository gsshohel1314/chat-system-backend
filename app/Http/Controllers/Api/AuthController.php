<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generate Sanctum token   
            $token = $user->createToken('user')->plainTextToken;

            DB::commit();

             return $this->successResponse([
                'token' => $token,
                'user'  => $user,
            ], 'User registered successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Registration failed. Please try again.', [
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Check user exists
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse('Invalid credentials.', [], 401);
        }

        // Generate Sanctum token
        $token = $user->createToken('user')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user'  => $user,
        ], 'User logged in successfully.');
    }

    public function logout(Request $request): JsonResponse
    {
        // Currently authenticated user
        $user = $request->user();

        // Revoke the current access token
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/** @group Authentication */
class AuthController extends ApiController
{
    /**
     * Register a customer.
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::query()->create($data);

        return $this->success([
            'user' => $this->userData($user),
            'token' => $user->createToken($data['device_name'])->plainTextToken,
        ], 'Account created successfully.', 201);
    }

    /**
     * Log in a customer.
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::query()->where('email', $data['email'])->first();

        if ($user === null || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'data' => null,
                'message' => 'The provided credentials are incorrect.',
                'errors' => ['email' => ['The provided credentials are incorrect.']],
            ], 422);
        }

        return $this->success([
            'user' => $this->userData($user),
            'token' => $user->createToken($data['device_name'])->plainTextToken,
        ], 'Signed in successfully.');
    }

    /** Retrieve the authenticated customer profile. */
    public function profile(Request $request): JsonResponse
    {
        return $this->success($this->userData($request->user()), 'Profile retrieved successfully.');
    }

    /** Revoke the current device token. */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success(null, 'Signed out successfully.');
    }

    private function userData(User $user): array
    {
        return $user->only(['id', 'name', 'email', 'phone']);
    }
}

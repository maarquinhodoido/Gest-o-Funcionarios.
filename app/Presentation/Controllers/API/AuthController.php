<?php

namespace App\Presentation\Controllers\API;

use App\Application\Services\AuthService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:3',
        ]);

        $result = $this->authService->attempt(
            email: $request->email,
            password: $request->password,
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        if (!$result) {
            return response()->json(['error' => 'Invalid credentials or account blocked'], 401);
        }

        return response()->json([
            'token' => $result['token'],
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $result['user'],
            'requires_2fa' => $result['requires_2fa'],
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        $token = $this->authService->refresh();
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->me();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        return response()->json(['data' => $user]);
    }
}

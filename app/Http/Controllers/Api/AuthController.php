<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {

        if (!Auth::attempt($request->validated())) {
                return $this->errorResponse(
                    'Invalid email or password.',
                    null,
                    401
                );
        }

            $user = Auth::user();

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse(
                'Login successful.',
                [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse('Logout successful.',null);
    }
}

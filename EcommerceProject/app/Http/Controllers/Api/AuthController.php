<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ){}

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = $this->repository->create(array_merge(
            $validatedData,
            [
                'role' => UserRole::USER,
                'avatar' => storeImage($validatedData['avatar'], 'avatars')
            ]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'user' => $user->only([
                'id',
                'email',
                'username',
                'first_name',
                'last_name',
                'name',
                'birthday',
                'avatar',
                'role',
                'created_at',
                'updated_at'
            ])
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if($token = Auth::guard('jwt')->attempt([
            fn($query) => $query->where('email', $credentials['username'])->orWhere('username', $credentials['username']),
            'password' => $credentials['password']
        ], true)) {
            return response()->json([
                'success' => true,
                'message' => "Login successful",
                'user' => $request->user('jwt')->only([
                    'email',
                    'username',
                    'first_name',
                    'last_name',
                    'name',
                    'birthday',
                    'avatar',
                    'role',
                    'created_at',
                    'updated_at'
                ]),
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid username or password.',
        ], 401);
    }

    public function logout(Request $request)
    {
        if(Auth::guard('jwt')->check()) {
            $invalidateToken = $request->exists('invalidate') && $request->boolean('invalidate');

            Auth::guard('jwt')->logout($invalidateToken);

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated user.',
        ], 401);
    }
}

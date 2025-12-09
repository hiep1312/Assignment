<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthController extends BaseApiController
{
    const PRIVATE_FIELDS = [...UserController::PRIVATE_FIELDS, 'name'];

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
                'avatar' => storeImage($validatedData['avatar'] ?? null, 'avatars')
            ]
        ));

        return $this->response(
            success: true,
            message: 'Registration successful.',
            code: 201,
            additionalData: ['user' => $user->only(self::PRIVATE_FIELDS)]
        );
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if($token = Auth::guard('jwt')->attempt([
            fn($query) => $query->where('email', $credentials['username'])->orWhere('username', $credentials['username']),
            'password' => $credentials['password']
        ], true)) {
            return $this->response(
                success: true,
                message: 'Login successful',
                code: 200,
                additionalData: [
                    'user' => $request->user('jwt')->only(self::PRIVATE_FIELDS),
                    'token' => $token,
                ]
            );
        }

        return $this->response(
            success: false,
            message: 'Invalid username or password.',
            code: 401
        );
    }

    public function logout(Request $request)
    {
        if(Auth::guard('jwt')->check()) {
            $invalidateToken = $request->exists('invalidate') && $request->boolean('invalidate');

            Auth::guard('jwt')->logout($invalidateToken);

            return $this->response(
                success: true,
                code: 200,
                message: 'Logged out successfully.',
            );
        }

        return $this->response(
            success: false,
            message: 'Unauthenticated user.',
            code: 401
        );
    }

    public function refresh(Request $request)
    {
        try {
            $newToken = Auth::guard('jwt')->refresh(false, false);
            $user = $request->user('jwt');

            return $this->response(
                success: true,
                code: 200,
                message: 'Token refreshed successfully.',
                additionalData: [
                    'user' => $user->only(self::PRIVATE_FIELDS),
                    'token' => $newToken,
                ]
            );

        }catch(TokenBlacklistedException $blacklistedException) {
            return $this->response(
                success: false,
                code: 403,
                message: 'Token has been invalidated. Please login again.'
            );

        }catch(Throwable $invalidException) {
            return $this->response(
                success: false,
                code: 401,
                message: 'Invalid token. Please login again.'
            );

        }
    }

    public function me(Request $request)
    {
        $user = $request->user('jwt');

        return $this->response(
            success: true,
            code: 200,
            message: 'User information retrieved successfully.',
            additionalData: ['user' => $user->only(self::PRIVATE_FIELDS)]
        );
    }
}

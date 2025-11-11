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

        if (Auth::attempt([
            fn($query) => $query->where('email', $credentials['username'])->orWhere('username', $credentials['username']),
            'password' => $credentials['password']
        ], $credentials['remember'] ?? false)) {
            /*
            * Check if the request has a session (SPA / stateful authentication)
            * If it does, regenerate the session ID to prevent session fixation attacks.
            * Otherwise, create a personal access token (for API / token-based authentication).
            */
            if($request->hasSession(false)){
                $request->session()->regenerate();
            }else{
                $token = $request->user()->createToken('auth_token', ['*'])->plainTextToken;
            }

            return response()->json([
                'success' => true,
                'message' => "Login successful" . ($request->hasSession(false) ? " (SPA)." : "."),
                'user' => $request->user()->only([
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
                ]),
                'token' => $token ?? null,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid username or password.',
        ], 401);
    }

    public function logout(Request $request)
    {
        if(Auth::guard('sanctum')->check()) {
            /*
            * Determine logout behavior based on request flags:
            * - logoutCurrentDevice: whether to log out the current device/session
            * - logoutOtherDevices: whether to log out all other sessions/devices
            * Initialize logoutOtherStatus as true to track the result of logging out other devices.
            */
            $logoutCurrentDevice = !$request->exists('logout_current') || $request->boolean('logout_current');
            $logoutOtherDevices = $request->exists('logout_other') && $request->boolean('logout_other');
            $logoutOtherStatus = true;

            if($request->hasSession(false)){
                // If requested, log out other devices after verifying the password
                if($logoutOtherDevices && $request->filled('password')){
                    $logoutOtherStatus = Auth::logoutOtherDevices($request->input('password'));
                }

                // Log out the current session if required
                if($logoutCurrentDevice && $logoutOtherStatus){
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
            }else{
                /*
                * API token-based logout:
                * - If logging out other devices, retrieve all personal access tokens for the user.
                * - If not logging out the current device, exclude the current access token from deletion.
                * - Delete the selected tokens from database.
                */
                if($logoutOtherDevices){
                    $tokensQuery = $request->user('sanctum')->tokens();

                    $logoutCurrentDevice ?: $tokensQuery->whereNot('id', $request->user('sanctum')->currentAccessToken()->id);
                    $tokensQuery->delete();
                }else{
                    $request->user('sanctum')->currentAccessToken()->delete();
                }
            }

            return response()->json([
                'success' => $logoutOtherStatus,
                'message' => $logoutOtherDevices
                    ? ($logoutOtherStatus
                        ? ($logoutCurrentDevice
                            ? 'Logged out from all devices successfully.'
                            : 'Logged out from other devices successfully.'
                        ) : 'Failed to log out other devices. Invalid password.')
                    : 'Logged out successfully.',
                'logout_current' => $logoutCurrentDevice,
                'logout_other' => $logoutOtherDevices,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated user.',
        ], 401);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    /**
     * Registracija
     */
    public function register(Request $request): JsonResponse
    {
        $role = $request->input('role');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:user,worker,admin',
        ];

        if ($role === 'worker') {
            $rules['shelter_id'] = ['required', 'exists:shelters,id'];
        } else {
            $rules['shelter_id'] = ['nullable'];
        }

        try {
            $validated = $request->validate($rules);
            $user = User::create($validated);

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            $user = JWTAuth::user();

            $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);

            // Cookies
            $accessCookie = cookie('access_token', $token, 60, '/', 'localhost', false, true);
            $refreshCookie = cookie('refresh_token', $refreshToken, 60 * 24 * 7, '/', 'localhost', false, true);


            return response()->json([
                'message' => 'Login successful',
                'access_token'   => $token,
                'email' => $user->email,
                'role'    => $user->role,
            ])->withCookie($accessCookie)
                ->withCookie($refreshCookie);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    /**
     * Logout
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::parseToken()->authenticate();
            JWTAuth::invalidate(JWTAuth::getToken());

            $forgetAccess = Cookie::forget('access_token', '/', 'localhost');
            $forgetRefresh = Cookie::forget('refresh_token', '/', 'localhost');



            return response()->json(['message' => 'Logged out successfully'], 200)->withCookie($forgetAccess)->withCookie($forgetRefresh);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not provided'], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh JWT token
     */
    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->cookie('refresh_token');

        if (!$refreshToken) {
            return response()->json(['error' => 'No refresh token'], 401);
        }

        try {
            $payload = JWTAuth::setToken($refreshToken)->getPayload();

            // Optional: ensure it’s a refresh token
            if ($payload->get('type') !== 'refresh') {
                return response()->json(['error' => 'Invalid refresh token'], 401);
            }

            $user = JWTAuth::authenticate($refreshToken);

            // Issue new access token
            $newAccessToken = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Access token refreshed',
                'access_token' => $newAccessToken
            ])->withCookie(cookie('refresh_token', $refreshToken, 60, '/', 'localhost', false, true));
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid or expired refresh token'], 401);
        }
    }
    // --- Current user info ---
    public function me(Request $request): JsonResponse
    {
        $accessToken = $request->cookie('access_token');
        if (!$accessToken) return response()->json(['error' => 'No access token'], 401);

        try {
            $user = JWTAuth::setToken($accessToken)->authenticate();
            return response()->json([
                'email' => $user->email,
                'role' => $user->role,
                'name' => $user->name
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
    public function updateProfile(Request $request)
    {
        $user = $request->user(); // currently authenticated user

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
            ]);

            $user->update($validated);

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update profile',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}

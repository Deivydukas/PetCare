<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class RefreshJwtToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Parse token and authenticate user
            $user = JWTAuth::parseToken()->authenticate();

            // Check if token is close to expiry (optional)
            $payload = JWTAuth::parseToken()->getPayload();
            $exp = $payload('exp'); // timestamp
            $now = now()->timestamp;

            // Refresh if less than 5 mins remaining
            if ($exp - $now < 300) {
                $newToken = JWTAuth::refresh(JWTAuth::getToken());
                // Send new token in response header
                $response = $next($request);
                $response->headers->set('Authorization', 'Bearer ' . $newToken);
                return $response;
            }

            return $next($request);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token invalid or expired',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}

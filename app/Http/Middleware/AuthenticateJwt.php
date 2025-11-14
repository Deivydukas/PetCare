<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateJwt
{
    public function handle(Request $request, Closure $next)
    {
        // Force JSON response for API
        $request->headers->set('Accept', 'application/json');

        // Let exceptions bubble up
        $user = JWTAuth::parseToken()->authenticate();

        // Set user for request
        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}

// class AuthenticateJwt
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle($request, Closure $next)
//     {
//         $user = JWTAuth::parseToken()->authenticate();
//         $request->setUserResolver(fn() => $user);

//         return $next($request);
//     }
// }


// class AuthenticateJwt
// {
//     public function handle(Request $request, Closure $next)
//     {
//         $request->headers->set('Accept', 'application/json');
//         try {
//             $user = JWTAuth::parseToken()->authenticate();
//         } catch (JWTException $e) {
//             return response()->json([
//                 'error' => 'Token missing, invalid, or expired'
//             ], 401);
//         }

//         // Set the user for the request
//         $request->setUserResolver(fn() => $user);

//         return $next($request);
//     }
// }

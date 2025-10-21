<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRequestsDuringMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->isDownForMaintenance()) {
            return response()->json(['message' => 'Service Unavailable'], 503);
        }
        return $next($request);
    }
}

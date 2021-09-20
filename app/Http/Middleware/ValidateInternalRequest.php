<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateInternalRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken() !== config('app.secret_internal_service_bearer_token')){
            return response()->json([
                'message' => __(('lang.You are not authorized to access this internal API'))
            ], 401);
        }
        return $next($request);
    }
}

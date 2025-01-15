<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VerifyJwtToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get(env('AUTH_SERVICE_URL') . '/api/auth');

            if ($response->failed()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $request->merge(['user' => $response->json('user')]);
            
            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Auth service unavailable'], 503);
        }
    }
} 
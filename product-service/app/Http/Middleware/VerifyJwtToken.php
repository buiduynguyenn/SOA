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
            // Gọi đến Auth Service để xác thực token
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get(env('AUTH_SERVICE_URL') . '/api/auth');

            if ($response->failed()) {
                return response()->json(['message' => 'Invalid token'], 401);
            }

            // Lưu thông tin user từ Auth Service vào request
            $request->merge(['user' => $response->json('user')]);
            
            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Auth service unavailable'], 503);
        }
    }
} 
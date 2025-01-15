<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'UserName' => 'required',
            'Password' => 'required'
        ]);

        $user = User::where('UserName', $request->UserName)->first();
        
        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $token = auth()->login($user);
        $user->Token = $token;
        $user->save();

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function checkAuth()
    {
        return response()->json([
            'message' => 'Token is valid',
            'user' => auth()->user()
        ]);
    }
} 
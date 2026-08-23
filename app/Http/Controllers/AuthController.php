<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            $user = Auth::user();
            return response()->json([
                'user' => $user,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ]);
        }

        return response()->json([
            'message' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out']);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'user' => $user,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }
}

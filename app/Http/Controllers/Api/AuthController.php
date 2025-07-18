<?php

namespace App\Http\Controllers\Api; // <-- Debe ser EXACTAMENTE así

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nomina' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['nomina' => $credentials['nomina'], 'password' => $credentials['password']])) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}

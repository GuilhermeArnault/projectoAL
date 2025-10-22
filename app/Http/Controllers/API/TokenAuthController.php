<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class TokenAuthController extends Controller
{
    // Login e geração de token
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            // opcional: 'device_name' => 'nullable|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $user = Auth::user();

        // cria token pessoal (nome opcional, por exemplo device)
        $token = $user->createToken($request->input('device_name', 'api-token'))->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ], 200);
    }

    // Logout: revoga tokens do utilizador atual
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            // revoga todos os tokens
            $user->tokens()->delete();
        }

        return response()->json(['message' => 'Token(s) revogados.'], 200);
    }
}


<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * 👤 Endpoint: /api/register
     * Regista um novo utilizador com role 'cliente' por defeito.
     */
    public function register(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $user->assignRole('cliente');

    Auth::login($user);

    return response()->json([
        'user' => $user,
        'message' => 'Registo efetuado com sucesso.',
    ], 201);
    }

    /**
     *  Endpoint: /api/login
     * Autentica um utilizador e emite um token Sanctum.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'user' => Auth::user(),
            'message' => 'Login efetuado com sucesso.',
        ]);
    }

    /**
     * Endpoint: /api/logout (Requer auth:sanctum)
     * Invalida o token atual.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout efetuado com sucesso.']);
    }

    /**
     *  Endpoint: /api/user (Requer auth:sanctum)
     * Retorna o utilizador autenticado.
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * 📝 Endpoint: /api/profile/update (Requer auth:sanctum)
     * Atualiza o nome e/ou email do utilizador autenticado.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            // O email deve ser único, exceto para o utilizador atual
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'Perfil atualizado com sucesso.'
        ]);
    }

    /**
     * Endpoint: /api/profile/password (Requer auth:sanctum)
     * Atualiza a password do utilizador autenticado.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Verifica se a password atual corresponde
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['A password atual fornecida não está correta.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password atualizada com sucesso.']);
    }
}

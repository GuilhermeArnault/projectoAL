<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CompleteRegistrationController extends Controller
{
    public function show(Request $request, $id, $hash)
    {
        // ✅ garante que o link é assinado e não expirou
        if (! $request->hasValidSignature()) {
            abort(403, 'Link inválido ou expirado.');
        }

        $user = User::findOrFail($id);

        // ✅ valida o hash do email
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Link inválido.');
        }

        // ✅ se já tiver concluído, não faz sentido mostrar o formulário
        if ($user->is_approved) {
            return redirect()->route('login')->with('status', 'Conta já ativa. Já podes fazer login.');
        }

        // ✅ marca o email como verificado ao abrir o link
        if (is_null($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => now()])->save();
            event(new Verified($user));
        }

        // ✅ gera URL assinada para o POST (form submit)
        $postUrl = URL::temporarySignedRoute(
            'complete-registration.store',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        return Inertia::render('Auth/CompleteRegistration', [
            'email' => $user->email,
            'postUrl' => $postUrl,
        ]);
    }

    public function store(Request $request, $id, $hash)
    {
        // ✅ também valida assinatura aqui (POST assinado)
        if (! $request->hasValidSignature()) {
            abort(403, 'Link inválido ou expirado.');
        }

        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Link inválido.');
        }

        // ✅ evita re-submeter se já estiver aprovado
        if ($user->is_approved) {
            return redirect()->route('login')->with('status', 'Conta já ativa. Já podes fazer login.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'nif' => [
                'required',
                'digits:9',
                Rule::unique('users', 'nif')->ignore($user->id),
            ],
        ]);

        $user->forceFill([
            'phone' => $validated['phone'],
            'nif' => $validated['nif'],
            'is_approved' => true,
            'approved_at' => now(),
        ])->save();

        return redirect()->route('login')->with('status', 'Registo concluído! Já podes fazer login.');
    }
}
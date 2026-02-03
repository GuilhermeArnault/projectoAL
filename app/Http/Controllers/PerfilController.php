<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'nif' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $user->updateProfilePhoto($request->file('profile_photo'));
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nif' => $validated['nif'],
        ]);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }
}

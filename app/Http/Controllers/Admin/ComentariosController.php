<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentariosController extends Controller
{
    public function index(Request $request)
    {
        $query = Comentario::with(['user', 'alojamento'])->latest();

        if ($s = $request->string('search')->toString()) {
            $query->where(function ($q) use ($s) {
                $q->where('titulo', 'like', "%{$s}%")
                  ->orWhere('texto', 'like', "%{$s}%");
            });
        }

        if (!is_null($request->aprovado)) {
            $query->where('aprovado', (bool)$request->aprovado);
        }

        $comentarios = $query->paginate(10)->withQueryString();

        return response()->json($comentarios);
    }

    public function destroy($id)
    {
        $comentario = Comentario::findOrFail($id);
        $comentario->delete();

        return response()->json(['message' => 'Comentário removido.']);
    }

    public function toggleAprovado($id)
    {
        $comentario = Comentario::findOrFail($id);
        $comentario->aprovado = !$comentario->aprovado;
        $comentario->save();

        return response()->json([
            'message' => 'Estado atualizado.',
            'aprovado' => $comentario->aprovado
        ]);
    }
}
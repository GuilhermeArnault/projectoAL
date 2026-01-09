<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Http\Requests\StoreComentarioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ComentarioController extends Controller
{
    // Listar todos os comentários
    public function index()
    {
        return response()->json(Comentario::all());
    }

    // Mostrar um comentário específico
    public function show($id)
    {
        return response()->json(Comentario::findOrFail($id));
    }

    //  Criar um novo comentário (com validação)
    public function store(StoreComentarioRequest $request)
    {
/*     $request->validate([
        'alojamento_id' => 'required|exists:alojamentos,id',
        'titulo' => 'required|string|max:255',
        'texto' => 'required|string',
        'rating' => 'required|integer|min:1|max:5',
    ]); */


    Comentario::create([
        'user_id' => auth()->id(),
        'alojamento_id' => $request->alojamento_id,
        'titulo' => $request->titulo,
        'texto' => $request->texto,
        'rating' => $request->rating,
        'aprovado' => false,
    ]);

    return response()->json([
        'message' => 'Comentário enviado com sucesso.'
    ], 201);
    }

    // Atualizar um comentário
    public function update(StoreComentarioRequest $request, $id)
    {
        $comentario = Comentario::findOrFail($id);
        $comentario->update($request->validated());
        return response()->json($comentario);
    }

    // Eliminar um comentário
    public function destroy($id)
    {
        $comentario = Comentario::findOrFail($id);
        $comentario->delete();
        return response()->json(['message' => 'Comentário eliminado com sucesso.']);
    }
}
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alojamento;

class AlojamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Alojamento::with(['fotos', 'videos'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Alojamento::with(['fotos', 'videos', 'comentarios' => function($q){
            $q->where('aprovado', true);
        }])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function reservas()
    {
        return Reserva::with(['user', 'alojamento'])->latest()->get();
    }

    public function alterarEstado(Request $request, $id)
    {
        $request->validate(['estado' => 'required|string']);
        $reserva = Reserva::findOrFail($id);
        $reserva->estado = $request->estado;
        $reserva->save();

        return response()->json(['message' => 'Estado atualizado.']);
    }

    public function relatorioOcupacao(Request $request)
    {
        $ano = $request->get('ano', now()->year);
        $dados = [];

        for ($mes = 1; $mes <= 12; $mes++) {
            $inicio = Carbon::create($ano, $mes, 1);
            $fim = $inicio->copy()->endOfMonth();

            $reservas = Reserva::where('estado', 'confirmado')
                ->whereBetween('checkin', [$inicio, $fim])
                ->get();

            $noites = $reservas->sum(fn($r) => Carbon::parse($r->checkin)->diffInDays(Carbon::parse($r->checkout)));

            $dados[] = [
                'mes' => $mes,
                'ocupacao_noites' => $noites,
            ];
        }

        return response()->json($dados);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
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

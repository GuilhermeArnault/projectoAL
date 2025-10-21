<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Alojamento;
use Carbon\Carbon;

class ReservaController extends Controller
{
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
$request->validate([
            'alojamento_id' => 'required|exists:alojamentos,id',
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'hospedes' => 'required|integer|min:1',
        ]);

        $start = Carbon::parse($request->checkin);
        $end = Carbon::parse($request->checkout);

        $aloj = Alojamento::findOrFail($request->alojamento_id);

        // Verificar disponibilidade
        if (!$this->available($request, $aloj->id)->getData()->available) {
            return response()->json(['message' => 'Datas indisponíveis.'], 409);
        }

        $noites = $start->diffInDays($end);
        $total = $noites * $aloj->preco_noite;

        $reserva = Reserva::create([
            'user_id' => auth()->id(),
            'alojamento_id' => $aloj->id,
            'checkin' => $start,
            'checkout' => $end,
            'hospedes' => $request->hospedes,
            'total' => $total,
            'estado' => 'pendente',
            'referencia' => strtoupper(uniqid('RES')),
        ]);

        return response()->json($reserva, 201);
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

    public function available(Request $request, $id)
    {
        $request->validate([
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
        ]);

        $start = Carbon::parse($request->checkin);
        $end = Carbon::parse($request->checkout);

        $overlap = Reserva::where('alojamento_id', $id)
            ->whereNotIn('estado', ['cancelado', 'expirado'])
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('checkin', [$start, $end])
                  ->orWhereBetween('checkout', [$start, $end])
                  ->orWhere(function($q2) use ($start, $end) {
                      $q2->where('checkin', '<', $start)
                         ->where('checkout', '>', $end);
                  });
            })
            ->exists();

        return response()->json(['available' => !$overlap]);
    }

        public function myReservations()
    {
        return auth()->user()->reservas()->with('alojamento')->latest()->get();
    }
}

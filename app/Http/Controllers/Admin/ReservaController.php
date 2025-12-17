<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Alojamento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservaController extends Controller
{
    /**
     * LISTAR + FILTROS + PAGINAÇÃO
     * GET /admin/api/reservas
     */
    public function index(Request $request)
    {
        // Filtros vindos da query string
        $search       = $request->input('search');          // ID ou referência
        $estado       = $request->input('estado');          // pendente, confirmado, cancelado, etc.
        $alojamentoId = $request->input('alojamento_id');   // ID do alojamento
        $perPage      = (int) $request->input('per_page', 10);

        $query = Reserva::with(['user', 'alojamento'])
            ->orderByDesc('created_at');

        // 🔍 Filtro por ID / referência
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('referencia', 'like', "%{$search}%");
            });
        }

        // 🎯 Filtro por estado (ignora "todas")
        if (!empty($estado) && $estado !== 'todas') {
            $query->where('estado', $estado);
        }

        // 🏠 Filtro por alojamento
        if (!empty($alojamentoId)) {
            $query->where('alojamento_id', $alojamentoId);
        }

        $reservas = $query->paginate($perPage);

        // Opções para o dropdown de alojamentos
        $alojamentos = Alojamento::orderBy('titulo')  // ajusta se o campo tiver outro nome
            ->get(['id', 'titulo']);

        // Estados possíveis (para o filtro)
        $estados = [
            'pendente',
            'confirmado',
            'concluido',
            'cancelado',
            'expirado',
        ];

        return response()->json([
            'reservas'    => $reservas,     // paginator
            'alojamentos' => $alojamentos,  // para dropdown
            'estados'     => $estados,      // para filtro
        ]);
    }

    /**
     * CRIAR RESERVA
     * POST /admin/api/reservas
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'alojamento_id' => 'required|exists:alojamentos,id',
            'checkin'       => 'required|date|after_or_equal:today',
            'checkout'      => 'required|date|after:checkin',
            'hospedes'      => 'required|integer|min:1',
            'estado'        => 'nullable|in:pendente,confirmado,concluido,cancelado,expirado',
            'observacoes'   => 'nullable|string',
        ]);

        // Calcula o total com o método estático do model
        $total = Reserva::calcularPreco(
            $data['checkin'],
            $data['checkout'],
            $data['alojamento_id']
        );

        // Gera referência única
        $referencia = $this->gerarReferenciaUnica();

        $reserva = Reserva::create([
            'user_id'       => $data['user_id'],
            'alojamento_id' => $data['alojamento_id'],
            'checkin'       => $data['checkin'],
            'checkout'      => $data['checkout'],
            'hospedes'      => $data['hospedes'],
            'total'         => $total,
            'estado'        => $data['estado'] ?? 'pendente',
            'referencia'    => $referencia,
            'observacoes'   => $data['observacoes'] ?? null,
        ]);

        $reserva->load(['user', 'alojamento']);

        return response()->json($reserva, 201);
    }

    /**
     * VER DETALHES DE UMA RESERVA
     * GET /admin/api/reservas/{reserva}
     */
    public function show(Reserva $reserva)
    {
        $reserva->load(['user', 'alojamento']);

        return response()->json($reserva);
    }

    /**
     * ATUALIZAR RESERVA COMPLETA
     * PUT /admin/api/reservas/{reserva}
     */
    public function update(Request $request, Reserva $reserva)
    {
        $data = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'alojamento_id' => 'required|exists:alojamentos,id',
            'checkin'       => 'required|date',
            'checkout'      => 'required|date|after:checkin',
            'hospedes'      => 'required|integer|min:1',
            'estado'        => 'required|in:pendente,confirmado,concluido,cancelado,expirado',
            'observacoes'   => 'nullable|string',
        ]);

        // Recalcular total se datas/alojamento mudaram
        $total = Reserva::calcularPreco(
            $data['checkin'],
            $data['checkout'],
            $data['alojamento_id']
        );

        $reserva->update([
            'user_id'       => $data['user_id'],
            'alojamento_id' => $data['alojamento_id'],
            'checkin'       => $data['checkin'],
            'checkout'      => $data['checkout'],
            'hospedes'      => $data['hospedes'],
            'total'         => $total,
            'estado'        => $data['estado'],
            'observacoes'   => $data['observacoes'] ?? null,
        ]);

        $reserva->load(['user', 'alojamento']);

        return response()->json($reserva);
    }

    /**
     * APAGAR RESERVA
     * DELETE /admin/api/reservas/{reserva}
     */
    public function destroy(Reserva $reserva)
    {
        $reserva->delete();

        return response()->json(null, 204);
    }

    /**
     * ALTERAR APENAS O ESTADO (AÇÕES RÁPIDAS)
     * PATCH /admin/api/reservas/{reserva}/estado
     */
    public function updateEstado(Request $request, Reserva $reserva)
    {
        $data = $request->validate([
            'estado' => 'required|in:pendente,confirmado,concluido,cancelado,expirado',
        ]);

        $reserva->estado = $data['estado'];
        $reserva->save();

        $reserva->load(['user', 'alojamento']);

        return response()->json($reserva);
    }

    /**
     * Gera uma referência única para a reserva
     */
    protected function gerarReferenciaUnica(): string
    {
        do {
            $ref = 'RES-' . strtoupper(Str::random(8));
        } while (Reserva::where('referencia', $ref)->exists());

        return $ref;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservaController extends Controller
{
    /**
     * Mostrar todas as reservas (GET /api/reservas) - Público/Geralmente Admin.
     * Na nossa estrutura, é melhor usar indexAdmin para o admin.
     * Este método pode ser removido se as rotas públicas não o usarem.
     */
    public function index()
    {
        // Nota: Se esta rota for pública, pode precisar de filtragem.
        // Se for para Admin, use indexAdmin abaixo.
        $reservas = Reserva::with(['user', 'alojamento'])->get();
        return response()->json($reservas);
    }

    /**
     * Mostrar uma reserva específica (GET /api/reservas/{id})
     */
    public function show($id)
    {
        $reserva = Reserva::with(['user', 'alojamento'])->findOrFail($id);
        return response()->json($reserva);
    }

    /**
     * Criar nova reserva (POST /api/reservas) - Cliente Autenticado.
     */
    public function store(Request $request)
    {
        // 🚨 Ajuste: O user_id deve ser injetado do utilizador autenticado, não validado como input.
        $data = $request->validate([
            'alojamento_id' => 'required|exists:alojamentos,id',
            'data_inicio' => 'required|date|after_or_equal:today',
            'data_fim' => 'required|date|after:data_inicio',
        ]);
        
        $data['user_id'] = auth()->id(); // Utilizador autenticado

        // 🔹 Verificar overlap de reservas
        $overlap = Reserva::where('alojamento_id', $data['alojamento_id'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('data_inicio', [$data['data_inicio'], $data['data_fim']])
                    ->orWhereBetween('data_fim', [$data['data_inicio'], $data['data_fim']])
                    // Verifica também se a nova reserva abrange datas de uma reserva existente
                    ->orWhere(function ($q) use ($data) {
                        $q->where('data_inicio', '<', $data['data_inicio'])
                          ->where('data_fim', '>', $data['data_fim']);
                    });
            })
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($overlap) {
            return response()->json(['error' => 'Já existe uma reserva nesse intervalo.'], 409);
        }

        // 🔹 Calcular preço (Assumindo que Reserva::calcularPreco existe no Model)
        $data['preco_total'] = Reserva::calcularPreco($data['data_inicio'], $data['data_fim'], $data['alojamento_id']);
        $data['estado'] = 'pendente';

        $reserva = Reserva::create($data);

        return response()->json($reserva, 201);
    }

    /**
     * Atualizar uma reserva (PUT /api/reservas/{id}) - Geralmente Admin
     */
    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        // 🚨 Recomendação: Para clientes, use o método 'cancel'.
        // Este 'update' deve ser limitado ao Admin, ou deve ter uma Gate/Policy para autorizar.
        // Por enquanto, vou deixá-lo como estava, mas com validação de estado.
        
        $data = $request->validate([
            'data_inicio' => 'sometimes|date',
            'data_fim' => 'sometimes|date|after:data_inicio',
            'estado' => ['sometimes', Rule::in(['pendente', 'confirmada', 'cancelada'])]
        ]);

        // Se mudar as datas, verificar overlap novamente
        if ($request->has(['data_inicio', 'data_fim'])) {
            $inicio = $request->input('data_inicio', $reserva->data_inicio);
            $fim = $request->input('data_fim', $reserva->data_fim);

            $overlap = Reserva::where('alojamento_id', $reserva->alojamento_id)
                ->where(function ($query) use ($inicio, $fim, $id) {
                    $query->whereBetween('data_inicio', [$inicio, $fim])
                        ->orWhereBetween('data_fim', [$inicio, $fim])
                        ->orWhere(function ($q) use ($inicio, $fim) {
                            $q->where('data_inicio', '<', $inicio)
                              ->where('data_fim', '>', $fim);
                        });
                })
                ->where('id', '!=', $id)
                ->where('estado', '!=', 'cancelada')
                ->exists();

            if ($overlap) {
                return response()->json(['error' => 'Já existe uma reserva nesse intervalo.'], 409);
            }

            // Recalcular preço se as datas mudarem
            $data['preco_total'] = Reserva::calcularPreco($inicio, $fim, $reserva->alojamento_id);
        }

        $reserva->update($data);

        return response()->json($reserva);
    }

    /**
     * ❌ Cancelar reserva (POST /api/reservas/{reserva}/cancel) - Cliente Autenticado.
     * Mais seguro que usar o destroy genérico, pois permite lógica específica de cancelamento.
     */
    public function cancel(Reserva $reserva)
    {
        // 🚨 Autorização: Verificar se o utilizador autenticado é o dono da reserva
        if (auth()->id() !== $reserva->user_id) {
            return response()->json(['message' => 'Não autorizado a cancelar esta reserva.'], 403);
        }

        // 🚨 Restrição: Impedir cancelamento se a data de início for muito próxima ou já tiver passado
        if (now()->greaterThanOrEqualTo($reserva->data_inicio)) {
            return response()->json(['message' => 'Não é possível cancelar uma reserva no dia de entrada ou após.'], 400);
        }
        
        $reserva->update(['estado' => 'cancelada']);
        
        // 📝 Adicionar lógica de log (tabela reserva_logs) e reembolso (se aplicável) aqui.
        
        return response()->json(['message' => 'Reserva cancelada com sucesso.']);
    }

    /**
     * 🗑️ Eliminar reserva (DELETE /api/reservas/{id}) - Geralmente Admin
     * Mantenho este método como um 'soft delete' de estado.
     */
    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        // Em vez de apagar, atualizamos para cancelada, para manter o histórico.
        $reserva->update(['estado' => 'cancelada']); 
        return response()->json(['message' => 'Reserva marcada como cancelada/eliminada.']);
    }

    /**
     * 👁️ Retorna as reservas do utilizador autenticado (GET /api/reservas/me) - Cliente.
     */
    public function myReservations(Request $request)
    {
        // O middleware 'auth:sanctum' garante que o utilizador está autenticado.
        $user = $request->user();

        $reservas = Reserva::with('alojamento')
            ->where('user_id', $user->id)
            ->latest() // Ordenar pelas mais recentes
            ->get();

        return response()->json($reservas);
    }

    /**
     * 🧑‍💻 Retorna TODAS as reservas (GET /api/admin/reservas) - Admin.
     */
    public function indexAdmin()
    {
        // O middleware 'admin' nas rotas garante que só o admin acede.
        $reservas = Reserva::with(['user', 'alojamento'])
            ->orderBy('data_inicio', 'desc')
            ->get();

        return response()->json($reservas);
    }

    /**
     * ✍️ Atualiza o estado de uma reserva (PUT /api/admin/reservas/{reserva}) - Admin.
     */
    public function updateStatus(Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado' => ['required', 'string', Rule::in(['pendente', 'confirmada', 'cancelada'])],
        ]);

        $novoEstado = $request->estado;

        if ($reserva->estado === $novoEstado) {
             return response()->json([
                'message' => 'O estado já é ' . $novoEstado,
                'reserva' => $reserva
            ], 200);
        }

        $reserva->update([
            'estado' => $novoEstado,
        ]);

        // 📝 Aqui seria o ponto para adicionar a entrada na tabela reserva_logs e enviar emails.

        return response()->json([
            'message' => "Estado da reserva #{$reserva->id} atualizado para {$novoEstado}.",
            'reserva' => $reserva->load(['user', 'alojamento'])
        ]);
    }
    
    /**
     * 🗓️ Verifica a disponibilidade (POST /api/alojamentos/{id}/available)
     * Método para clientes verificarem se podem reservar um intervalo de datas.
     */
    public function available(Request $request, $alojamentoId)
    {
        $data = $request->validate([
            'data_inicio' => 'required|date|after_or_equal:today',
            'data_fim' => 'required|date|after:data_inicio',
        ]);

        // Verificação de overlap ajustada
        $overlap = Reserva::where('alojamento_id', $alojamentoId)
            ->where(function ($query) use ($data) {
                $query->whereBetween('data_inicio', [$data['data_inicio'], $data['data_fim']])
                    ->orWhereBetween('data_fim', [$data['data_inicio'], $data['data_fim']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('data_inicio', '<', $data['data_inicio'])
                          ->where('data_fim', '>', $data['data_fim']);
                    });
            })
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($overlap) {
            return response()->json(['available' => false, 'message' => 'O alojamento não está disponível neste período.'], 200);
        }

        return response()->json(['available' => true, 'message' => 'O alojamento está disponível!'], 200);
    }
}

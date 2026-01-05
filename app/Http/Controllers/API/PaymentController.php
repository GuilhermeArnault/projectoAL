<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Pagamento;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;



class PaymentController extends Controller
{
    public function checkout($reservaId)
    {
        $reserva = Reserva::with('user')->findOrFail($reservaId);

        $pagamento = Pagamento::create([
            'reserva_id' => $reserva->id,
            'valor' => $reserva->total,
            'estado' => 'pendente',
        ]);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "EUR",
                    "value" => $reserva->total
                ]
            ]],
            "application_context" => [
                "return_url" => route('paypal.success', $pagamento),
                "cancel_url" => route('paypal.cancel', $pagamento),
            ]
        ]);

        $pagamento->update([
            'referencia' => $order['id']
        ]);

        foreach ($order['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return response()->json(['payment_url' => $link['href']]);
            }
        }

        return response()->json(['error' => 'Erro PayPal'], 500);
    }


public function success(Request $request, Pagamento $pagamento)
{
    // ESTE É O ORDER ID REAL
    $orderId = $request->query('token');

    if (!$orderId) {
        abort(400, 'Order ID em falta');
    }

    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $provider->getAccessToken();

    // CAPTURA REAL
    $result = $provider->capturePaymentOrder($orderId);

    if (($result['status'] ?? null) !== 'COMPLETED') {
        return redirect('/perfil/reservas')
            ->with('error', 'Pagamento não foi concluído.');
    }

    // Atualizar pagamento
    $pagamento->update([
        'estado' => 'pago',
        'data_pagamento' => now(),
        'metodo' => 'paypal',
        'referencia' => $orderId,
    ]);

    // Confirmar reserva
    $pagamento->reserva->update([
        'estado' => 'confirmado'
    ]);

    return redirect('/')
        ->with('success', 'Pagamento efetuado com sucesso!');
}


    public function cancel(Pagamento $pagamento)
    {
        return redirect('/perfil/reservas')
            ->with('error', 'Pagamento cancelado.');
    }
}


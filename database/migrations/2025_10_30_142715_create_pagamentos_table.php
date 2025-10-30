<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reserva_id')->index('pagamentos_reserva_id_foreign');
            $table->decimal('valor', 10);
            $table->enum('metodo', ['stripe', 'paypal', 'transferencia', 'manual'])->default('manual');
            $table->enum('estado', ['pendente', 'pago', 'falhado', 'reembolsado'])->default('pendente');
            $table->string('referencia')->nullable();
            $table->timestamp('data_pagamento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};

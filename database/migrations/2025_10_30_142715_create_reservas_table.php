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
        Schema::create('reservas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('reservas_user_id_foreign');
            $table->unsignedBigInteger('alojamento_id')->index('reservas_alojamento_id_foreign');
            $table->date('checkin');
            $table->date('checkout');
            $table->integer('hospedes')->default(1);
            $table->decimal('total', 10);
            $table->enum('estado', ['pendente', 'confirmado', 'concluido', 'cancelado', 'expirado'])->default('pendente');
            $table->string('referencia')->unique();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};

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
        Schema::table('reserva_logs', function (Blueprint $table) {
            $table->foreign(['reserva_id'])->references(['id'])->on('reservas')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserva_logs', function (Blueprint $table) {
            $table->dropForeign('reserva_logs_reserva_id_foreign');
        });
    }
};

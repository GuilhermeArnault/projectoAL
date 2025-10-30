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
        Schema::create('bloqueios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('alojamento_id')->index('bloqueios_alojamento_id_foreign');
            $table->date('inicio');
            $table->date('fim');
            $table->string('motivo')->nullable();
            $table->unsignedBigInteger('created_by')->index('bloqueios_created_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloqueios');
    }
};

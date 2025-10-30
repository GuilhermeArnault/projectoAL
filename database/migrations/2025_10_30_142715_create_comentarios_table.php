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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('comentarios_user_id_foreign');
            $table->unsignedBigInteger('alojamento_id')->index('comentarios_alojamento_id_foreign');
            $table->string('titulo')->nullable();
            $table->tinyInteger('rating')->default(5);
            $table->text('texto')->nullable();
            $table->boolean('aprovado')->default(false);
            $table->text('resposta_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};

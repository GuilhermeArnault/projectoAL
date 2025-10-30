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
        Schema::table('bloqueios', function (Blueprint $table) {
            $table->foreign(['alojamento_id'])->references(['id'])->on('alojamentos')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bloqueios', function (Blueprint $table) {
            $table->dropForeign('bloqueios_alojamento_id_foreign');
            $table->dropForeign('bloqueios_created_by_foreign');
        });
    }
};

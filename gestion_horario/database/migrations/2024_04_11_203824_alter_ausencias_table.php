<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        Schema::table('ausencias', function (Blueprint $table) {
            // Verificar si la columna 'horario_id' ya existe antes de intentar agregarla
            if (!Schema::hasColumn('ausencias', 'horario_id')) {
                $table->unsignedBigInteger('horario_id');
                $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('cascade');
            }

            if (!Schema::hasColumn('ausencias', 'user_id')) {
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ausencias', function (Blueprint $table) {
            $table->dropForeign(['horario_id']);
            $table->dropColumn('horario_id');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};

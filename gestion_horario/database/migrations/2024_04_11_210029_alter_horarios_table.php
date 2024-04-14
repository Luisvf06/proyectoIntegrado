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
        Schema::table('horarios', function (Blueprint $table) {
            $table->unsignedBigInteger('franja_id')->nullable(); // Asegúrate de que pueda ser null si no siempre se proporciona un valor
            $table->foreign('franja_id')->references('id')->on('franjas')->onDelete('cascade');

            $table->unsignedBigInteger('asignatura_id')->nullable();
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');

            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('aula_id')->nullable();
            $table->foreign('aula_id')->references('id')->on('aulas')->onDelete('cascade');

            $table->unsignedBigInteger('periodo_id')->nullable();
            $table->foreign('periodo_id')->references('id')->on('periodos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropForeign(['franja_id']);
            $table->dropColumn('franja_id');

            $table->dropForeign(['asignatura_id']);
            $table->dropColumn('asignatura_id');

            $table->dropForeign(['grupo_id']);
            $table->dropColumn('grupo_id');

            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->dropForeign(['aula_id']);
            $table->dropColumn('aula_id');

            $table->dropForeign(['periodo_id']);
            $table->dropColumn('periodo_id');
        });
    }
};

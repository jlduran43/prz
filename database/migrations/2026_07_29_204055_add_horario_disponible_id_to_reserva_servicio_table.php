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
        Schema::table('reserva_servicio', function (Blueprint $table) {
            $table->foreignId('horario_disponible_id')
                ->nullable()
                ->after('servicio_experiencia_id')
                ->constrained('horarios_disponibles')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserva_servicio', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'horario_disponible_id'
            );
        });
    }
};

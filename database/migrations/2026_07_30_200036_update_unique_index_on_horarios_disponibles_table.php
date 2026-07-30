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
        Schema::table(
            'horarios_disponibles',
            function (Blueprint $table) {
                $table->dropUnique(
                    'horarios_disponibles_hora_inicio_hora_termino_unique'
                );

                $table->unique(
                    [
                        'servicio_experiencia_id',
                        'fecha',
                        'hora_inicio',
                        'hora_termino',
                    ],
                    'horarios_servicio_fecha_horas_unique'
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'horarios_disponibles',
            function (Blueprint $table) {
                $table->dropUnique(
                    'horarios_servicio_fecha_horas_unique'
                );

                $table->unique(
                    [
                        'hora_inicio',
                        'hora_termino',
                    ],
                    'horarios_disponibles_hora_inicio_hora_termino_unique'
                );
            }
        );
    }
};

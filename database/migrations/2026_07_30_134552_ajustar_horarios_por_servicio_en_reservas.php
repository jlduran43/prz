<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Eliminar horario general de reservas
        |--------------------------------------------------------------------------
        */
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign([
                'horario_disponible_id',
            ]);

            $table->dropIndex(
                'reservas_fecha_hora_inicio_hora_termino_index'
            );

            $table->dropColumn([
                'horario_disponible_id',
                'hora_inicio',
                'hora_termino',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Agregar índice simple para la fecha
        |--------------------------------------------------------------------------
        */
        Schema::table('reservas', function (Blueprint $table) {
            $table->index('fecha');
        });

        /*
        |--------------------------------------------------------------------------
        | Hacer obligatorio el horario por servicio
        |--------------------------------------------------------------------------
        */
        Schema::table('reserva_servicio', function (Blueprint $table) {
            $table
                ->foreignId('horario_disponible_id')
                ->nullable(false)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('reserva_servicio', function (Blueprint $table) {
            $table
                ->foreignId('horario_disponible_id')
                ->nullable()
                ->change();
        });

        Schema::table('reservas', function (Blueprint $table) {
            $table->dropIndex([
                'fecha',
            ]);

            $table
                ->foreignId('horario_disponible_id')
                ->nullable()
                ->constrained('horarios_disponibles');

            $table->time('hora_inicio')->nullable();
            $table->time('hora_termino')->nullable();

            $table->index([
                'fecha',
                'hora_inicio',
                'hora_termino',
            ]);
        });
    }
};

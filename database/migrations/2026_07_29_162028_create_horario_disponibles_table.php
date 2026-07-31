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
        Schema::create('horarios_disponibles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('servicio_experiencia_id')
                ->constrained('servicios_experiencias')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('capacidad_maxima')
                ->nullable();

            $table->time('hora_inicio');
            $table->time('hora_termino');
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(
                [
                    'servicio_experiencia_id',
                    'hora_inicio',
                    'hora_termino',
                ],
                'horarios_servicio_horas_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario_disponibles');
    }
};

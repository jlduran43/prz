<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'horario_servicio',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId(
                    'horario_disponible_id'
                )
                    ->constrained(
                        'horarios_disponibles'
                    )
                    ->cascadeOnDelete();

                $table->foreignId(
                    'servicio_experiencia_id'
                )
                    ->constrained(
                        'servicios_experiencias'
                    )
                    ->cascadeOnDelete();

                $table->timestamps();

                $table->unique(
                    [
                        'horario_disponible_id',
                        'servicio_experiencia_id',
                    ],
                    'horario_servicio_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'horario_servicio'
        );
    }
};

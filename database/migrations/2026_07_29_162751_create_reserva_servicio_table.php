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
        Schema::create(
            'reserva_servicios',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('reserva_id')
                    ->constrained('reservas')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('servicio_experiencia_id')
                    ->constrained('servicios_experiencias');

                $table
                    ->foreignId('horario_disponible_id')
                    ->constrained('horarios_disponibles');

                $table
                    ->decimal('precio', 12, 2)
                    ->default(0);

                $table->timestamps();

                $table->unique([
                    'reserva_id',
                    'servicio_experiencia_id',
                ]);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva_servicio');
    }
};

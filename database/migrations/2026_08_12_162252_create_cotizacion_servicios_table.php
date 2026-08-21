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
            'cotizacion_servicios',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('cotizacion_id')
                    ->constrained('cotizaciones')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('servicio_experiencia_id')
                    ->constrained('servicios_experiencias')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                /*
                 * Guardamos también el nombre.
                 *
                 * De esa manera, si mañana cambia
                 * el nombre del servicio, la cotización
                 * histórica mantiene el nombre original.
                 */

                $table->string('nombre_servicio', 150);
                $table->string('tipo_cobro', 30);

                $table->decimal('precio_unitario', 12, 2);
                $table->decimal('subtotal', 12, 2);

                $table->unsignedInteger('cantidad_asistentes');
                $table->unsignedInteger('personas_pagadas');
                $table->unsignedInteger('entradas_liberadas')->default(0);

                $table->unsignedTinyInteger('orden')->default(1);

                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_servicios');
    }
};

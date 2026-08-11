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
        Schema::create('servicios_experiencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_servicio_id')
                ->constrained('categorias_servicio')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();

            $table->unsignedInteger('duracion_minutos')->nullable();
            $table->unsignedInteger('capacidad_minima')->nullable();
            $table->unsignedInteger('capacidad_maxima')->nullable();

            $table->unsignedTinyInteger('max_cursos_simultaneos')->default(1);

            $table->unsignedInteger('max_alumnos_por_curso')->nullable();

            $table->decimal('precio', 12, 2)->default(0);

            $table->enum('tipo_cobro', [
                'POR_PERSONA',
                'POR_GRUPO',
            ])->default('POR_PERSONA');


            $table->boolean('requiere_reserva')->default(true);
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios_experiencias');
    }
};

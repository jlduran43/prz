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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_cliente_id')
                ->constrained('tipos_cliente');

            $table->string('nombres', 100)->nullable();
            $table->string('apellidos', 100)->nullable();
            $table->string('rut_persona', 20)->nullable();

            $table->string('nombre_entidad', 150)->nullable();
            $table->string('rut_entidad', 20)->nullable();
            $table->string('nombre_encargado', 150)->nullable();
            $table->string('rut_encargado', 20)->nullable();

            $table->string('email', 150);
            $table->string('telefono', 30);

            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regiones');

            $table->foreignId('comuna_id')
                ->nullable()
                ->constrained('comunas');

            $table->unsignedInteger('cantidad_asistentes');

            $table->unsignedInteger('cantidad_alumnos')
                ->nullable();

            $table->unsignedInteger('cantidad_profesores')
                ->nullable();

            $table->string('nivel_educacional', 50)
                ->nullable();

            $table->string('curso', 100)
                ->nullable();

            $table->foreignId('convenio_id')
                ->nullable()
                ->constrained('convenios')
                ->nullOnDelete();

            $table->string('codigo_convenio', 50)
                ->nullable();

            $table->string('nombre_convenio', 150)
                ->nullable();

            $table->decimal(
                'porcentaje_descuento',
                5,
                2
            )->default(0);

            $table->decimal('subtotal', 12, 2)
                ->default(0);

            $table->decimal('descuento', 12, 2)
                ->default(0);

            $table->decimal('total', 12, 2)
                ->default(0);

            $table->string('estado', 30)
                ->default('PENDIENTE');

            $table->text('observaciones')
                ->nullable();

            $table->text('objetivo_visita')
                ->nullable();

            $table->timestamps();

            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};

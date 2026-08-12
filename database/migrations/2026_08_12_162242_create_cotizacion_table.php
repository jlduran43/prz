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
        Schema::create('cotizaciones', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | IDENTIFICACIÓN
            |--------------------------------------------------------------------------
            */
            $table->string('folio', 20)
                ->nullable()
                ->unique();

            $table->string('token_acceso', 64)
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | TIPO DE CLIENTE
            |--------------------------------------------------------------------------
            */
            $table->foreignId('tipo_cliente_id')
                ->constrained('tipos_cliente')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | PERSONA NATURAL
            |--------------------------------------------------------------------------
            */
            $table->string('nombres', 100)->nullable();
            $table->string('apellidos', 100)->nullable();
            $table->string('rut_persona', 20)->nullable();

            /*
            |--------------------------------------------------------------------------
            | ORGANIZACIÓN
            |--------------------------------------------------------------------------
            */
            $table->string('nombre_entidad', 150)->nullable();
            $table->string('rut_entidad', 20)->nullable();

            $table->string('nombre_encargado', 150)->nullable();
            $table->string('rut_encargado', 20)->nullable();

            /*
            |--------------------------------------------------------------------------
            | CONTACTO
            |--------------------------------------------------------------------------
            */
            $table->string('email', 150);
            $table->string('telefono', 30);

            /*
            |--------------------------------------------------------------------------
            | UBICACIÓN
            |--------------------------------------------------------------------------
            */
            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regiones')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('comuna_id')
                ->nullable()
                ->constrained('comunas')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | ASISTENTES
            |--------------------------------------------------------------------------
            */
            $table->unsignedInteger('cantidad_asistentes');

            /*
            |--------------------------------------------------------------------------
            | DATOS EDUCACIONALES
            |--------------------------------------------------------------------------
            */
            $table->unsignedInteger('cantidad_alumnos')
                ->nullable();

            $table->unsignedInteger('cantidad_profesores')
                ->nullable();

            $table->string('nivel_educacional', 50)
                ->nullable();

            $table->string('curso', 100)
                ->nullable();

            $table->text('objetivo_visita')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | CONVENIO
            |--------------------------------------------------------------------------
            */
            $table->foreignId('convenio_id')
                ->nullable()
                ->constrained('convenios')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            /*
             * Estos campos son una "fotografía" del convenio.
             * Si el convenio cambia después, la cotización conserva
             * los valores utilizados al momento de emitirla.
             */
            $table->string('codigo_convenio', 50)
                ->nullable();

            $table->string('nombre_convenio', 150)
                ->nullable();

            $table->decimal(
                'porcentaje_descuento',
                5,
                2
            )->default(0);

            /*
            |--------------------------------------------------------------------------
            | TOTALES
            |--------------------------------------------------------------------------
            */
            $table->decimal('subtotal', 12, 2);

            $table->decimal('descuento', 12, 2)
                ->default(0);

            $table->decimal('total', 12, 2);

            /*
            |--------------------------------------------------------------------------
            | ESTADO
            |--------------------------------------------------------------------------
            */
            $table->enum('estado', [
                'EMITIDA',
                'CONVERTIDA_RESERVA',
                'ANULADA',
            ])->default('EMITIDA');

            /*
            |--------------------------------------------------------------------------
            | FECHAS
            |--------------------------------------------------------------------------
            */
            $table->timestamp('fecha_emision');

            $table->date('fecha_vencimiento')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacions');
    }
};

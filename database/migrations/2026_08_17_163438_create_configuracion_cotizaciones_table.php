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
        Schema::create('configuraciones_cotizacion', function (Blueprint $table) {
            $table->id();

            $table->string('titulo')->default('Condiciones de la reserva');
            $table->string('titular_cuenta')->nullable();
            $table->string('rut_titular', 20)->nullable();
            $table->string('banco')->nullable();
            $table->string('tipo_cuenta')->nullable();
            $table->string('numero_cuenta')->nullable();
            $table->string('correo_comprobantes')->nullable();
            $table->string('correo_reservas')->nullable();
            $table->string('telefono_reservas')->nullable();
            $table->string('horario_contacto')->nullable();

            $table->text('descripcion_tour')->nullable();
            $table->text('condiciones_pago')->nullable();
            $table->text('politica_devoluciones')->nullable();
            $table->text('condiciones_museo')->nullable();
            $table->text('recomendaciones_museo')->nullable();
            $table->text('recomendaciones_parque')->nullable();
            $table->text('nota_importante')->nullable();

            $table->unsignedInteger('dias_validez')->default(30);

            $table->boolean('activo')->default(true); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_cotizaciones');
    }
};

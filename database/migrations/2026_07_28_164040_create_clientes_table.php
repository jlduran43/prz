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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_cliente_id')
                ->constrained('tipos_cliente')
                ->restrictOnDelete();
            $table->foreignId('region_id')
                ->constrained('regiones')
                ->restrictOnDelete();
            $table->foreignId('comuna_id')
                ->constrained('comunas')
                ->restrictOnDelete();

            $table->string('email', 150);
            $table->string('telefono', 30);
             $table->string('direccion', 200)->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

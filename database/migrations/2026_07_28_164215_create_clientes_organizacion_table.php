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
        Schema::create('clientes_organizacion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->unique()
                ->constrained('clientes')
                ->cascadeOnDelete();

            $table->string('nombre_organizacion', 150);
            $table->string('rut_organizacion', 20)->unique();

            $table->string('nombre_encargado', 150);
            $table->string('rut_encargado', 20)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_organizacion');
    }
};

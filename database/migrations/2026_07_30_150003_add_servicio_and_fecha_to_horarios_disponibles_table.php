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
        Schema::table(
            'horarios_disponibles',
            function (Blueprint $table) {
                $table->foreign(
                    'servicio_experiencia_id'
                )
                    ->references('id')
                    ->on('servicios_experiencias')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'horarios_disponibles',
            function (Blueprint $table) {
                $table->dropForeign([
                    'servicio_experiencia_id',
                ]);
            }
        );
    }
};

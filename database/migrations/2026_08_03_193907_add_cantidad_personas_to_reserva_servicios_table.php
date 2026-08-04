<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'reserva_servicios',
            function (Blueprint $table) {
                $table->unsignedInteger('cantidad_personas')
                    ->default(1)
                    ->after('horario_disponible_id');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'reserva_servicios',
            function (Blueprint $table) {
                $table->dropColumn('cantidad_personas');
            }
        );
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'servicios_experiencias',
            function (Blueprint $table) {
                $table->unsignedTinyInteger(
                    'max_cursos_simultaneos'
                )
                    ->default(1)
                    ->after('capacidad_maxima');

                $table->unsignedInteger(
                    'max_alumnos_por_curso'
                )
                    ->nullable()
                    ->after('max_cursos_simultaneos');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'servicios_experiencias',
            function (Blueprint $table) {
                $table->dropColumn([
                    'max_cursos_simultaneos',
                    'max_alumnos_por_curso',
                ]);
            }
        );
    }
};

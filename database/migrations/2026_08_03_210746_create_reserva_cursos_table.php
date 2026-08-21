<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'reserva_cursos',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('reserva_id')
                    ->constrained('reservas')
                    ->cascadeOnDelete();

                $table->string('curso', 100);
                $table->string('nivel_educacional',30);

                $table->unsignedInteger('cantidad_alumnos');
                $table->unsignedInteger('cantidad_profesores')->default(0);

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva_cursos');
    }
};

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
        Schema::create(
            'convenio_entidades',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('convenio_id')
                    ->constrained('convenios')
                    ->cascadeOnDelete();

                $table->string('nombre_entidad', 150);

                /*
                 * RUT para mostrar.
                 * Ejemplo: 76.123.456-7
                 */

                $table->string('rut_entidad', 20);

                /*
                 * RUT sin puntos ni guion.
                 * Ejemplo: 761234567
                 *
                 * Lo utilizaremos para comparar.
                 */

                $table->string('rut_normalizado', 20);

                $table->boolean('activo')
                    ->default(true);

                $table->timestamps();

                $table->unique([
                    'convenio_id',
                    'rut_normalizado',
                ]);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convenio_entidades');
    }
};

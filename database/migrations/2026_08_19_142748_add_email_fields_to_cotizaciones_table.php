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
        Schema::table('cotizaciones', function (Blueprint $table) {

            $table->timestamp('correo_enviado_at')
                ->nullable()
                ->after('estado');

            $table->text('correo_error')
                ->nullable()
                ->after('correo_enviado_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->dropColumn([
                'correo_enviado_at',
                'correo_error',
            ]);
        });
    }
};

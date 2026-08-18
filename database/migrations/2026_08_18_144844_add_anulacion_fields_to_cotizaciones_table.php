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

            $table
                ->timestamp('anulada_at')
                ->nullable();

            $table->string('anulada_por_tipo', 20)
                ->nullable();

            $table
                ->text('motivo_anulacion')
                ->nullable();

            $table
                ->foreignId('anulada_por_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {

            $table
                ->dropConstrainedForeignId(
                    'anulada_por_user_id'
                );

            $table->dropColumn([
                'anulada_at',
                'anulada_por_tipo',
                'motivo_anulacion',
            ]);
        });
    }
};

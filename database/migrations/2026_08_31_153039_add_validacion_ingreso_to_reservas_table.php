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
        Schema::table('reservas', function (Blueprint $table) {

            $table->timestamp('validada_at')
                ->nullable()
                ->after('ticket_enviado_at');

            $table->foreignId('validada_por_user_id')
                ->nullable()
                ->after('validada_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {

            $table->dropForeign([
                'validada_por_user_id'
            ]);

            $table->dropColumn([
                'validada_at',
                'validada_por_user_id',
            ]);
        });
    }
};

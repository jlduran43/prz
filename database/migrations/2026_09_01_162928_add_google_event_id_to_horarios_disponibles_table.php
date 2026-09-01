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
        Schema::table('horarios_disponibles', function (Blueprint $table) {
            $table
                ->string('google_event_id', 255)
                ->nullable()
                ->after('activo');

            $table
                ->timestamp('google_synced_at')
                ->nullable()
                ->after('google_event_id');

            $table
                ->text('google_sync_error')
                ->nullable()
                ->after('google_synced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios_disponibles', function (Blueprint $table) {
            //
        });
    }
};

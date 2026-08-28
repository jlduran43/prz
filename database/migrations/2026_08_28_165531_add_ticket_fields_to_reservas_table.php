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

            $table->string('token_ticket', 64)
                ->nullable()
                ->unique()
                ->after('pagada_at');

            $table->timestamp('ticket_enviado_at')
                ->nullable()
                ->after('token_ticket');

            $table->text('ticket_email_error')
                ->nullable()
                ->after('ticket_enviado_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {

            $table->dropUnique([
                'token_ticket'
            ]);

            $table->dropColumn([
                'token_ticket',
                'ticket_enviado_at',
                'ticket_email_error',
            ]);
        });
    }
};

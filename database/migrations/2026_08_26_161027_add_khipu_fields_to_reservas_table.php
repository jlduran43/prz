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

            $table->string('medio_pago', 30)
                ->nullable()
                ->after('estado');

            $table->string('khipu_payment_id')
                ->nullable()
                ->unique();

            $table->string('khipu_transaction_id')
                ->nullable()
                ->unique();

            $table->timestamp('pagada_at')
                ->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {

            $table->dropColumn([
                'medio_pago',
                'khipu_payment_id',
                'khipu_transaction_id',
                'pagada_at',
            ]);

        });
    }
};

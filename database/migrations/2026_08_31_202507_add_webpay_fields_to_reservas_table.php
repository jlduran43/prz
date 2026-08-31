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

            $table->string('webpay_token', 255)
                ->nullable()
                ->index()
                ->after('medio_pago');

            $table->string('webpay_buy_order', 255)
                ->nullable()
                ->after('webpay_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {

            $table->dropIndex([
                'webpay_token',
            ]);

            $table->dropColumn([
                'webpay_token',
                'webpay_buy_order',
            ]);
        });
    }
};

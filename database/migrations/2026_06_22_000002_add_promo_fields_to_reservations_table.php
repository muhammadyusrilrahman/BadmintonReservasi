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
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('promo_code_id')->nullable()->after('total_price')->constrained('promo_codes')->nullOnDelete();
            $table->unsignedInteger('original_price')->nullable()->after('promo_code_id')->comment('Harga asli sebelum diskon');
            $table->unsignedInteger('discount_amount')->default(0)->after('original_price')->comment('Nominal potongan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['promo_code_id']);
            $table->dropColumn(['promo_code_id', 'original_price', 'discount_amount']);
        });
    }
};

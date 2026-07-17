<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambah kolom booking_session_id ke reservations dan payments.
     * Semua reservasi dari 1 sesi booking akan berbagi booking_session_id yang sama,
     * dan hanya 1 Payment yang dibuat per sesi.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // UUID sesi booking — semua reservasi dari 1 pemesanan bersamaan berbagi nilai ini
            $table->string('booking_session_id', 36)->nullable()->after('notes')->index();
        });

        Schema::table('payments', function (Blueprint $table) {
            // Linked ke sesi booking — memungkinkan 1 payment mewakili banyak reservasi
            $table->string('booking_session_id', 36)->nullable()->after('reservation_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['booking_session_id']);
            $table->dropColumn('booking_session_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['booking_session_id']);
            $table->dropColumn('booking_session_id');
        });
    }
};

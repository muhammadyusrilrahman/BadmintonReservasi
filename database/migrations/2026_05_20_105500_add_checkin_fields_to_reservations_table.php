<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Reservation;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('booking_code', 10)->unique()->nullable()->after('status');
            $table->timestamp('checked_in_at')->nullable()->after('notes');
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete()->after('checked_in_at');
        });

        // Generate booking codes for existing reservations
        Reservation::whereNull('booking_code')->each(function ($reservation) {
            $reservation->update(['booking_code' => 'ADN-' . strtoupper(Str::random(5))]);
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['checked_in_by']);
            $table->dropColumn(['booking_code', 'checked_in_at', 'checked_in_by']);
        });
    }
};

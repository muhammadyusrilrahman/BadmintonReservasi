<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();         // Label tampilan di UI
            $table->string('group')->default('general'); // general | payment | notification
            $table->string('type')->default('text');     // text | number | boolean | time | textarea
            $table->timestamps();
        });

        // Seed nilai default
        $now = now();
        DB::table('app_settings')->insert([
            // === UMUM ===
            ['key' => 'app_name',            'value' => 'Adenia Salsa Badminton',      'label' => 'Nama Aplikasi',          'group' => 'general',      'type' => 'text',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_phone',           'value' => '085248867071',                'label' => 'Nomor Telepon',          'group' => 'general',      'type' => 'text',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_email',           'value' => 'adenia.badminton@gmail.com',  'label' => 'Email',                  'group' => 'general',      'type' => 'text',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_address',         'value' => 'Jl. Contoh No. 1, Kota Anda','label' => 'Alamat',                 'group' => 'general',      'type' => 'textarea', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'operating_hours_open','value' => '06:00',                       'label' => 'Jam Buka',               'group' => 'general',      'type' => 'time',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'operating_hours_close','value' => '22:00',                      'label' => 'Jam Tutup',              'group' => 'general',      'type' => 'time',     'created_at' => $now, 'updated_at' => $now],
            // === PEMBAYARAN ===
            ['key' => 'bank_name',           'value' => 'BCA',                         'label' => 'Nama Bank',              'group' => 'payment',      'type' => 'text',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'bank_account_name',   'value' => 'Adenia Salsa',                'label' => 'Nama Pemilik Rekening',  'group' => 'payment',      'type' => 'text',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'bank_account_number', 'value' => '1234567890',                  'label' => 'Nomor Rekening',         'group' => 'payment',      'type' => 'text',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'payment_deadline',    'value' => '60',                          'label' => 'Batas Waktu Bayar (menit)', 'group' => 'payment',   'type' => 'number',   'created_at' => $now, 'updated_at' => $now],
            // === NOTIFIKASI ===
            ['key' => 'notif_email_booking', 'value' => '1',                           'label' => 'Email: Booking Baru',    'group' => 'notification', 'type' => 'boolean',  'created_at' => $now, 'updated_at' => $now],
            ['key' => 'notif_email_confirm', 'value' => '1',                           'label' => 'Email: Konfirmasi Pembayaran', 'group' => 'notification', 'type' => 'boolean', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'notif_email_cancel',  'value' => '1',                           'label' => 'Email: Pembatalan',      'group' => 'notification', 'type' => 'boolean',  'created_at' => $now, 'updated_at' => $now],
            ['key' => 'notif_wa_booking',    'value' => '0',                           'label' => 'WhatsApp: Booking Baru', 'group' => 'notification', 'type' => 'boolean',  'created_at' => $now, 'updated_at' => $now],
            ['key' => 'notif_wa_reminder',   'value' => '0',                           'label' => 'WhatsApp: Pengingat Jadwal', 'group' => 'notification', 'type' => 'boolean', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};

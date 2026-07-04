<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan.
     */
    public function index()
    {
        $settings = AppSetting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Simpan semua pengaturan dari form.
     */
    public function update(Request $request)
    {
        $request->validate([
            'app_name'             => 'required|string|max:100',
            'app_phone'            => 'required|string|max:20',
            'app_email'            => 'required|email|max:100',
            'app_address'          => 'nullable|string|max:500',
            'operating_hours_open' => 'required|date_format:H:i',
            'operating_hours_close'=> 'required|date_format:H:i|after:operating_hours_open',
            'bank_name'            => 'required|string|max:50',
            'bank_account_name'    => 'required|string|max:100',
            'bank_account_number'  => 'required|string|max:30',
            'payment_deadline'     => 'required|integer|min:5|max:1440',
        ]);

        // Daftar key yang boleh disimpan
        $keys = [
            // Umum
            'app_name', 'app_phone', 'app_email', 'app_address',
            'operating_hours_open', 'operating_hours_close',
            // Pembayaran
            'bank_name', 'bank_account_name', 'bank_account_number', 'payment_deadline',
            // Notifikasi (checkbox — kalau tidak dicentang, tidak ada di request)
            'notif_email_booking', 'notif_email_confirm', 'notif_email_cancel',
            'notif_wa_booking', 'notif_wa_reminder',
        ];

        $notifKeys = [
            'notif_email_booking', 'notif_email_confirm', 'notif_email_cancel',
            'notif_wa_booking', 'notif_wa_reminder',
        ];

        foreach ($keys as $key) {
            if (in_array($key, $notifKeys)) {
                // Checkbox: simpan 1 jika ada, 0 jika tidak
                AppSetting::set($key, $request->has($key) ? '1' : '0');
            } else {
                AppSetting::set($key, $request->input($key, ''));
            }
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['reservation_id', 'amount', 'payment_method', 'payment_proof', 'status', 'paid_at', 'verified_by', 'verified_at', 'snap_token', 'midtrans_transaction_id', 'payment_type'])]
/**
 * Payment Model — Mewakili data pembayaran untuk sebuah reservasi.
 *
 * Mendukung pembayaran manual (upload bukti transfer) dan otomatis
 * via Midtrans Snap Gateway (QRIS, Bank Transfer, E-Wallet).
 *
 * @property int $id
 * @property int $reservation_id
 * @property int $amount
 * @property string $payment_method
 * @property string|null $payment_proof
 * @property string $status pending|paid|failed|refunded
 * @property string|null $snap_token Token Midtrans Snap (cached)
 * @property string|null $midtrans_transaction_id ID transaksi dari Midtrans
 * @property string|null $payment_type Tipe pembayaran Midtrans (qris, gopay, bank_transfer, dll)
 * @property \Carbon\Carbon|null $paid_at
 * @property int|null $verified_by
 * @property \Carbon\Carbon|null $verified_at
 *
 * @property-read \App\Models\Reservation $reservation
 * @property-read \App\Models\User|null $verifiedBy
 * @property-read string $method_label
 * @property-read string $status_label
 * @property-read string $formatted_amount
 */
class Payment extends Model
{
    use LogsActivity;

    const METHOD_LABELS = [
        'cash'     => 'Tunai',
        'transfer' => 'Transfer Bank',
        'ewallet'  => 'E-Wallet',
    ];

    const STATUS_LABELS = [
        'pending'  => 'Menunggu',
        'paid'     => 'Lunas',
        'failed'   => 'Gagal',
        'refunded' => 'Dikembalikan',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'integer',
            'paid_at'     => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ──────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────

    public function getMethodLabelAttribute(): string
    {
        if ($this->payment_type) {
            return match($this->payment_type) {
                'qris' => 'QRIS',
                'gopay' => 'GoPay',
                'shopeepay' => 'ShopeePay',
                'bank_transfer' => 'Transfer Bank (Otomatis)',
                'cstore' => 'Gerai Retail',
                'credit_card' => 'Kartu Kredit',
                default => ucfirst(str_replace('_', ' ', $this->payment_type)),
            };
        }
        return self::METHOD_LABELS[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // ──────────────────────────────────────
    // Activity Log
    // ──────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount', 'payment_method', 'paid_at', 'verified_by', 'midtrans_transaction_id', 'payment_type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $event) => "Pembayaran #{$this->id} telah di-{$event}");
    }
}

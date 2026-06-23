<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['code', 'description', 'discount_type', 'discount_value', 'max_discount', 'valid_from', 'valid_until', 'max_usage', 'usage_count', 'is_active', 'activation_mode', 'created_by'])]
class PromoCode extends Model
{
    use LogsActivity;

    const DISCOUNT_TYPE_LABELS = [
        'percentage' => 'Persentase (%)',
        'fixed'      => 'Nominal Tetap (Rp)',
    ];

    const ACTIVATION_MODE_LABELS = [
        'manual' => 'Manual',
        'auto'   => 'Otomatis',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'integer',
            'max_discount'   => 'integer',
            'max_usage'      => 'integer',
            'usage_count'    => 'integer',
            'is_active'      => 'boolean',
            'valid_from'     => 'datetime',
            'valid_until'    => 'datetime',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    /**
     * Pembuat promo.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Reservasi yang menggunakan promo ini.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // ──────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────

    /**
     * Promo yang statusnya aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Promo yang sedang berlaku (aktif + dalam periode).
     */
    public function scopeCurrentlyValid(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());
    }

    /**
     * Filter berdasarkan mode aktivasi.
     */
    public function scopeByActivationMode(Builder $query, string $mode): Builder
    {
        return $query->where('activation_mode', $mode);
    }

    // ──────────────────────────────────────
    // Business Logic
    // ──────────────────────────────────────

    /**
     * Cek apakah promo saat ini valid untuk digunakan.
     */
    public function isCurrentlyValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (now()->lt($this->valid_from) || now()->gt($this->valid_until)) {
            return false;
        }

        if ($this->max_usage !== null && $this->usage_count >= $this->max_usage) {
            return false;
        }

        return true;
    }

    /**
     * Hitung nominal diskon berdasarkan harga asli.
     */
    public function calculateDiscount(int $originalPrice): int
    {
        if ($this->discount_type === 'percentage') {
            $discount = (int) floor($originalPrice * $this->discount_value / 100);

            // Batasi dengan max_discount jika ada
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }

            return $discount;
        }

        // Fixed discount — tidak boleh melebihi harga asli
        return min($this->discount_value, $originalPrice);
    }

    /**
     * Mendapatkan label status saat ini.
     */
    public function getCurrentStatusAttribute(): string
    {
        if (now()->gt($this->valid_until)) {
            return 'expired';
        }

        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->max_usage !== null && $this->usage_count >= $this->max_usage) {
            return 'exhausted';
        }

        if (now()->lt($this->valid_from)) {
            return 'scheduled';
        }

        return 'active';
    }

    // ──────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            $text = $this->discount_value . '%';
            if ($this->max_discount) {
                $text .= ' (maks Rp ' . number_format($this->max_discount, 0, ',', '.') . ')';
            }
            return $text;
        }

        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }

    public function getDiscountTypeLabelAttribute(): string
    {
        return self::DISCOUNT_TYPE_LABELS[$this->discount_type] ?? $this->discount_type;
    }

    public function getActivationModeLabelAttribute(): string
    {
        return self::ACTIVATION_MODE_LABELS[$this->activation_mode] ?? $this->activation_mode;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->current_status) {
            'active'    => 'Aktif',
            'inactive'  => 'Nonaktif',
            'expired'   => 'Kedaluwarsa',
            'exhausted' => 'Habis Terpakai',
            'scheduled' => 'Terjadwal',
            default     => 'Tidak Diketahui',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->current_status) {
            'active'    => 'emerald',
            'inactive'  => 'slate',
            'expired'   => 'red',
            'exhausted' => 'amber',
            'scheduled' => 'blue',
            default     => 'slate',
        };
    }

    // ──────────────────────────────────────
    // Activity Log
    // ──────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'discount_type', 'discount_value', 'is_active', 'valid_from', 'valid_until', 'max_usage'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $event) => "Kode promo '{$this->code}' telah di-{$event}");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['user_id', 'court_id', 'date', 'start_time', 'end_time', 'duration_hours', 'total_price', 'status', 'notes', 'booking_code', 'checked_in_at', 'checked_in_by', 'reschedule_count'])]
class Reservation extends Model
{
    use LogsActivity;

    /**
     * Status labels in Bahasa Indonesia.
     */
    const STATUS_LABELS = [
        'pending'   => 'Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    const STATUS_COLORS = [
        'pending'   => 'amber',
        'confirmed' => 'blue',
        'completed' => 'emerald',
        'cancelled' => 'red',
    ];

    protected function casts(): array
    {
        return [
            'date'             => 'date',
            'duration_hours'   => 'integer',
            'total_price'      => 'integer',
            'checked_in_at'    => 'datetime',
            'reschedule_count' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Reservation $reservation) {
            if (!$reservation->booking_code) {
                do {
                    $code = 'ADN-' . strtoupper(Str::random(5));
                } while (static::where('booking_code', $code)->exists());

                $reservation->booking_code = $code;
            }
        });
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Staff yang memproses check-in.
     */
    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    /**
     * Histori status dan reschedule reservasi.
     */
    public function statusLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReservationStatusLog::class);
    }

    /**
     * Data refund reservasi.
     */
    public function refund(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Refund::class);
    }

    /**
     * Review/ulasan untuk reservasi ini.
     */
    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Apakah reservasi bisa di-reschedule.
     */
    public function canReschedule(): bool
    {
        // Harus confirmed
        if ($this->status !== 'confirmed') {
            return false;
        }

        // Maksimal 1x
        if ($this->reschedule_count >= 1) {
            return false;
        }

        // Tidak boleh ada pengajuan refund aktif
        if ($this->refund && in_array($this->refund->status, ['requested', 'approved'])) {
            return false;
        }

        // Minimal H-1 (kemarin atau sebelumnya dibanding tanggal main)
        return now()->startOfDay()->diffInDays($this->date->startOfDay(), false) >= 1;
    }

    /**
     * Apakah reservasi bisa diajukan refund.
     */
    public function canRequestRefund(): bool
    {
        // Harus confirmed
        if ($this->status !== 'confirmed') {
            return false;
        }

        // Tidak boleh ada refund aktif
        if ($this->refund && in_array($this->refund->status, ['requested', 'approved', 'completed'])) {
            return false;
        }

        // Minimal H-1
        return now()->startOfDay()->diffInDays($this->date->startOfDay(), false) >= 1;
    }

    // ──────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('date', '>=', today())->where('status', '!=', 'cancelled');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Reservasi yang siap di-check-in (confirmed + hari ini).
     */
    public function scopeReadyForCheckIn(Builder $builder): Builder
    {
        return $builder->where('status', 'confirmed')
            ->whereDate('date', today());
    }

    /**
     * Reservasi yang sudah di-check-in.
     */
    public function scopeCheckedIn(Builder $builder): Builder
    {
        return $builder->where('status', 'completed')
            ->whereNotNull('checked_in_at');
    }

    // ──────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'slate';
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Apakah reservasi sudah di-check-in.
     */
    public function getIsCheckedInAttribute(): bool
    {
        return $this->checked_in_at !== null;
    }

    // ──────────────────────────────────────
    // Activity Log
    // ──────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'date', 'court_id', 'user_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $event) => "Reservasi #{$this->id} telah di-{$event}");
    }
}

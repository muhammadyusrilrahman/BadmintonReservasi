<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'reservation_id',
    'user_id',
    'amount',
    'reason',
    'bank_name',
    'account_number',
    'account_name',
    'status',
    'admin_notes',
    'processed_by',
    'processed_at',
    'completed_at'
])]
class Refund extends Model
{
    const STATUS_REQUESTED = 'requested';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    const STATUS_LABELS = [
        self::STATUS_REQUESTED => 'Diajukan',
        self::STATUS_APPROVED  => 'Disetujui',
        self::STATUS_REJECTED  => 'Ditolak',
        self::STATUS_COMPLETED => 'Selesai',
    ];

    const STATUS_COLORS = [
        self::STATUS_REQUESTED => 'amber',
        self::STATUS_APPROVED  => 'blue',
        self::STATUS_REJECTED  => 'red',
        self::STATUS_COMPLETED => 'emerald',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'integer',
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'slate';
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}

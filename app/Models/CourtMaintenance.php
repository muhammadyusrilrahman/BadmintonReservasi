<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['court_id', 'staff_id', 'title', 'description', 'status', 'scheduled_date', 'completed_at'])]
class CourtMaintenance extends Model
{
    const STATUS_LABELS = [
        'scheduled'   => 'Dijadwalkan',
        'in_progress' => 'Sedang Berjalan',
        'completed'   => 'Selesai',
    ];

    const STATUS_COLORS = [
        'scheduled'   => 'amber',
        'in_progress' => 'blue',
        'completed'   => 'emerald',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'completed_at'   => 'datetime',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // ──────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────

    public function scopePending($query)
    {
        return $query->whereIn('status', ['scheduled', 'in_progress']);
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
}

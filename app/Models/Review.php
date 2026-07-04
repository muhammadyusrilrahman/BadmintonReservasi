<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'reservation_id',
        'user_id',
        'court_id',
        'rating',
        'comment',
        'is_hidden',
        'admin_reply',
        'admin_reply_at',
    ];

    protected function casts(): array
    {
        return [
            'rating'         => 'integer',
            'is_hidden'      => 'boolean',
            'admin_reply_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    // ──────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────

    /** Hanya review yang tampil ke publik */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeByCourt($query, int $courtId)
    {
        return $query->where('court_id', $courtId);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Apakah reservasi ini boleh diberi review oleh customer.
     * Syarat:
     *  - Status completed
     *  - Tanggal main sudah lewat minimal 1 hari
     *  - Belum ada review untuk reservasi ini
     */
    public static function canReview(Reservation $reservation): bool
    {
        if ($reservation->status !== 'completed') {
            return false;
        }

        // Tanggal main harus sudah lewat (minimal kemarin)
        if ($reservation->date->startOfDay()->gte(now()->startOfDay())) {
            return false;
        }

        // Belum pernah review
        return ! static::where('reservation_id', $reservation->id)->exists();
    }

    /** Label bintang */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}

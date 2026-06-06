<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['name', 'type', 'description', 'price_per_hour', 'photo', 'is_active'])]
class Court extends Model
{
    use LogsActivity;

    /**
     * Court type labels in Bahasa Indonesia.
     */
    const TYPE_LABELS = [
        'rubber'    => 'Karet',
        'synthetic' => 'Sintetis',
        'wood'      => 'Kayu',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'price_per_hour' => 'integer',
            'is_active'      => 'boolean',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CourtSchedule::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(CourtMaintenance::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ──────────────────────────────────────
    // Query Scopes
    // ──────────────────────────────────────

    /**
     * Only active courts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ──────────────────────────────────────
    // Accessors / Helpers
    // ──────────────────────────────────────

    /**
     * Get human-readable type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Formatted price with Rupiah prefix.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_hour, 0, ',', '.');
    }

    /**
     * Photo URL — returns asset URL or null.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    // ──────────────────────────────────────
    // Activity Log
    // ──────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'type', 'price_per_hour', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $event) => "Lapangan {$this->name} telah di-{$event}");
    }
}

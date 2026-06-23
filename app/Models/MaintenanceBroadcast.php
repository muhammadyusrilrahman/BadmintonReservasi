<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceBroadcast extends Model
{
    protected $fillable = [
        'sender_id',
        'type',
        'court_id',
        'title',
        'description',
        'scheduled_date',
        'duration',
        'target_type',
        'recipients_count',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'recipients_count' => 'integer',
    ];

    /**
     * Get the user who sent the broadcast.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the court associated with the broadcast (if any).
     */
    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }
}

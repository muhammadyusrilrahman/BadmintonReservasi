<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Reservation;
use App\Notifications\MaintenanceBroadcastNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendMaintenanceBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $broadcastData
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $type = $this->broadcastData['type']; // system or court
        $targetType = $this->broadcastData['target_type']; // all or affected
        $scheduledDate = $this->broadcastData['scheduled_date'];
        $courtId = $this->broadcastData['court_id'] ?? null;
        $title = $this->broadcastData['title'];
        $description = $this->broadcastData['description'];
        $duration = $this->broadcastData['duration'];
        $courtName = $this->broadcastData['court_name'] ?? null;

        // Query users based on target type
        if ($targetType === 'affected') {
            if ($type === 'court' && $courtId) {
                // Get user IDs of pending/confirmed reservations on this court & scheduled date
                $userIds = Reservation::where('court_id', $courtId)
                    ->whereDate('date', $scheduledDate)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->pluck('user_id')
                    ->unique()
                    ->toArray();

                $users = User::whereIn('id', $userIds)->get();
            } else {
                // System maintenance: Get user IDs of pending/confirmed reservations on the scheduled date
                $userIds = Reservation::whereDate('date', $scheduledDate)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->pluck('user_id')
                    ->unique()
                    ->toArray();

                $users = User::whereIn('id', $userIds)->get();
            }
        } else {
            // Default: All users with the 'customer' role
            $users = User::role('customer')->get();
        }

        // Send notifications
        if ($users->isNotEmpty()) {
            Notification::send($users, new MaintenanceBroadcastNotification(
                $type,
                $title,
                $description,
                $scheduledDate,
                $duration,
                $courtName
            ));
        }
    }
}

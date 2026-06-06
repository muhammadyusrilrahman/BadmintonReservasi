<?php

namespace App\Listeners;

use App\Models\UserActivityLog;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;
        $request = request();

        if ($user) {
            UserActivityLog::create([
                'user_id'     => $user->id,
                'activity'    => 'User logout dari sistem 🔒',
                'method'      => 'POST',
                'url'         => $request->fullUrl(),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'properties'  => [
                    'email' => $user->email,
                ],
            ]);
        }
    }
}

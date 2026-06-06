<?php

namespace App\Listeners;

use App\Models\UserActivityLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $request = request();

        UserActivityLog::create([
            'user_id'     => $user->id,
            'activity'    => 'User login ke dalam sistem 🔑',
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

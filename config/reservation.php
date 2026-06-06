<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reservation Expiry Duration (Minutes)
    |--------------------------------------------------------------------------
    | Duration in minutes before an unpaid reservation is automatically cancelled.
    | This value is used by: MidtransService (Snap token expiry),
    | ExpireReservationJob (delayed job dispatch), and
    | CancelExpiredReservations (artisan command).
    */
    'expiry_minutes' => env('RESERVATION_EXPIRY_MINUTES', 15),
];

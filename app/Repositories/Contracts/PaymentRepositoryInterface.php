<?php

namespace App\Repositories\Contracts;

use App\Models\Payment;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find payment by reservation ID.
     */
    public function findByReservationId(int $reservationId): ?Payment;

    /**
     * Find payment by snap token.
     */
    public function findBySnapToken(string $snapToken): ?Payment;
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    protected array $with = ['reservation'];

    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    public function findByReservationId(int $reservationId): ?Payment
    {
        return $this->model->newQuery()
            ->where('reservation_id', $reservationId)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findBySnapToken(string $snapToken): ?Payment
    {
        return $this->model->newQuery()
            ->where('snap_token', $snapToken)
            ->first();
    }
}

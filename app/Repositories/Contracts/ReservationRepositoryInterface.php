<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface ReservationRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get paginated reservations with search and filters.
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $date = null,
        ?string $status = null,
        ?int $courtId = null
    ): LengthAwarePaginator;
}

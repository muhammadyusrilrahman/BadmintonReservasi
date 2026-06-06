<?php

namespace App\Repositories\Contracts;

use App\Models\Court;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CourtRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all active courts.
     */
    public function getActive(): Collection;

    /**
     * Get paginated courts with optional search & filter.
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $type = null,
        ?bool $isActive = null
    ): LengthAwarePaginator;

    /**
     * Check if a court is available on a given date and time range.
     */
    public function isAvailable(int $courtId, string $date, string $startTime, string $endTime, ?int $excludeReservationId = null): bool;

    /**
     * Find a court record and lock the row for update.
     */
    public function findWithLock(int $courtId): ?Court;
}

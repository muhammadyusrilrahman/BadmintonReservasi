<?php

namespace App\Repositories\Eloquent;

use App\Models\Court;
use App\Repositories\Contracts\CourtRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CourtRepository extends BaseRepository implements CourtRepositoryInterface
{
    /**
     * Relations to eager load by default.
     */
    protected array $with = [];

    public function __construct(Court $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all active courts, ordered by name.
     */
    public function getActive(): Collection
    {
        return $this->model->newQuery()
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Paginated courts with search (name) and filters (type, is_active).
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $type = null,
        ?bool $isActive = null
    ): LengthAwarePaginator {
        return $this->model->newQuery()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($type,   fn($q) => $q->where('type', $type))
            ->when(!is_null($isActive), fn($q) => $q->where('is_active', $isActive))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Check court availability — no overlapping confirmed/pending reservations.
     * Uses robust mathematical overlapping interval logic: start_time < endTime AND end_time > startTime.
     */
    public function isAvailable(
        int $courtId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeReservationId = null
    ): bool {
        return !$this->model->newQuery()
            ->whereHas('reservations', function ($q) use ($date, $startTime, $endTime, $excludeReservationId) {
                $q->whereDate('date', $date)
                  ->whereIn('status', ['pending', 'confirmed'])
                  ->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime)
                  ->when($excludeReservationId, fn($q) => $q->where('id', '!=', $excludeReservationId));
            })
            ->where('id', $courtId)
            ->exists();
    }

    /**
     * Find a court record and lock the row for update.
     */
    public function findWithLock(int $courtId): ?Court
    {
        return $this->model->newQuery()
            ->lockForUpdate()
            ->find($courtId);
    }
}

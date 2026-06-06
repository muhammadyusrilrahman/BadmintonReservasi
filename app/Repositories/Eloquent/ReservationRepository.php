<?php

namespace App\Repositories\Eloquent;

use App\Models\Reservation;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ReservationRepository extends BaseRepository implements ReservationRepositoryInterface
{
    /**
     * Relations to eager load by default.
     */
    protected array $with = ['user', 'court', 'payment'];

    public function __construct(Reservation $model)
    {
        parent::__construct($model);
    }

    /**
     * Get paginated reservations with search and filters.
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $date = null,
        ?string $status = null,
        ?int $courtId = null
    ): LengthAwarePaginator {
        return $this->model->newQuery()
            ->with($this->with)
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($date, fn($q) => $q->whereDate('date', $date))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($courtId, fn($q) => $q->where('court_id', $courtId))
            ->latest('date')
            ->latest('start_time')
            ->paginate($perPage)
            ->withQueryString();
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\PromoCode;
use App\Repositories\Contracts\PromoCodeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PromoCodeRepository extends BaseRepository implements PromoCodeRepositoryInterface
{
    public function __construct(PromoCode $model)
    {
        parent::__construct($model);
    }

    /**
     * Get paginated promo codes with optional search and status filter.
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $status = null
    ): LengthAwarePaginator {
        $query = $this->model->newQuery()->with('creator');

        // Search by code or description
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status) {
            switch ($status) {
                case 'active':
                    $query->where('is_active', true)
                          ->where('valid_from', '<=', now())
                          ->where('valid_until', '>=', now());
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('valid_until', '<', now());
                    break;
                case 'scheduled':
                    $query->where('is_active', true)
                          ->where('valid_from', '>', now());
                    break;
            }
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Find a promo code by its code string.
     */
    public function findByCode(string $code): ?PromoCode
    {
        return $this->model->newQuery()
            ->where('code', strtoupper(trim($code)))
            ->first();
    }

    /**
     * Get all promo codes with 'auto' activation mode.
     */
    public function getAutoActivatable(): Collection
    {
        return $this->model->newQuery()
            ->where('activation_mode', 'auto')
            ->get();
    }
}

<?php

namespace App\Repositories\Contracts;

use App\Models\PromoCode;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PromoCodeRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get paginated promo codes with filters.
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $status = null
    ): LengthAwarePaginator;

    /**
     * Find promo code by its code string.
     */
    public function findByCode(string $code): ?PromoCode;

    /**
     * Get promo codes with 'auto' activation mode for sync.
     */
    public function getAutoActivatable(): Collection;
}

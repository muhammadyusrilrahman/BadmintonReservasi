<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Repositories\Contracts\PromoCodeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PromoCodeService extends BaseService
{
    public function __construct(PromoCodeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get paginated promo codes with filters.
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $status = null
    ): LengthAwarePaginator {
        return $this->repository->getPaginatedFiltered($perPage, $search, $status);
    }

    /**
     * Override hook: normalize code to uppercase before creating.
     */
    protected function beforeCreate(array $data): array
    {
        $data['code'] = strtoupper(trim($data['code']));
        $data['created_by'] = $data['created_by'] ?? auth()->id();

        return $data;
    }

    /**
     * Override hook: normalize code to uppercase before updating.
     */
    protected function beforeUpdate(int|string $id, array $data): array
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper(trim($data['code']));
        }

        return $data;
    }

    /**
     * Toggle promo active status (for manual activation mode).
     */
    public function toggleActive(int $id): PromoCode
    {
        $promo = $this->repository->findOrFail($id);
        $promo->update(['is_active' => !$promo->is_active]);

        return $promo->fresh();
    }

    /**
     * Validate promo code and calculate discount.
     *
     * @return array{promo: PromoCode, discount: int, message: string}
     * @throws InvalidArgumentException
     */
    public function validateAndApply(string $code, int $originalPrice): array
    {
        $promo = $this->repository->findByCode($code);

        if (!$promo) {
            throw new InvalidArgumentException('Kode promo tidak ditemukan.');
        }

        if (!$promo->is_active) {
            throw new InvalidArgumentException('Kode promo tidak aktif.');
        }

        if (now()->lt($promo->valid_from)) {
            throw new InvalidArgumentException('Kode promo belum berlaku. Berlaku mulai ' . $promo->valid_from->format('d M Y H:i') . '.');
        }

        if (now()->gt($promo->valid_until)) {
            throw new InvalidArgumentException('Kode promo sudah kedaluwarsa.');
        }

        if ($promo->max_usage !== null && $promo->usage_count >= $promo->max_usage) {
            throw new InvalidArgumentException('Kode promo sudah habis terpakai.');
        }

        $discount = $promo->calculateDiscount($originalPrice);

        return [
            'promo'    => $promo,
            'discount' => $discount,
            'message'  => "Promo \"{$promo->code}\" berhasil diterapkan! Diskon {$promo->formatted_discount}.",
        ];
    }

    /**
     * Increment usage count after a promo is successfully used.
     */
    public function incrementUsage(PromoCode $promo): void
    {
        $promo->increment('usage_count');
    }

    /**
     * Sync auto-activation/deactivation of promo codes based on their date range.
     *
     * @return array{activated: int, deactivated: int}
     */
    public function syncAutoActivation(): array
    {
        $promos = $this->repository->getAutoActivatable();
        $activated = 0;
        $deactivated = 0;

        foreach ($promos as $promo) {
            $shouldBeActive = now()->gte($promo->valid_from)
                && now()->lte($promo->valid_until)
                && ($promo->max_usage === null || $promo->usage_count < $promo->max_usage);

            if ($shouldBeActive && !$promo->is_active) {
                $promo->update(['is_active' => true]);
                $activated++;
            } elseif (!$shouldBeActive && $promo->is_active) {
                $promo->update(['is_active' => false]);
                $deactivated++;
            }
        }

        return [
            'activated'   => $activated,
            'deactivated' => $deactivated,
        ];
    }

    /**
     * Delete promo code with guard — reject if already used.
     */
    protected function beforeDelete(int|string $id): void
    {
        $promo = $this->repository->findOrFail($id);

        if ($promo->usage_count > 0) {
            throw new InvalidArgumentException("Kode promo \"{$promo->code}\" tidak dapat dihapus karena sudah digunakan {$promo->usage_count}x.");
        }
    }
}

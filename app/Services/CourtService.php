<?php

namespace App\Services;

use App\Models\Court;
use App\Repositories\Contracts\CourtRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class CourtService extends BaseService
{
    public function __construct(CourtRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get paginated courts for admin index with filters.
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $type = null,
        ?bool $isActive = null
    ): LengthAwarePaginator {
        return $this->repository->getPaginatedFiltered($perPage, $search, $type, $isActive);
    }

    /**
     * Get all active courts (for dropdowns, booking, etc.).
     */
    public function getActiveCourts()
    {
        return $this->repository->getActive();
    }

    /**
     * Hook: handle photo upload before creation.
     */
    protected function beforeCreate(array $data): array
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $data['photo']->store('courts', 'public');
        }

        return $data;
    }

    /**
     * Hook: handle photo upload/replacement before update.
     */
    protected function beforeUpdate(int|string $id, array $data): array
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            // Delete old photo
            $court = $this->repository->find($id);
            if ($court && $court->photo) {
                Storage::disk('public')->delete($court->photo);
            }

            $data['photo'] = $data['photo']->store('courts', 'public');
        } else {
            // Don't overwrite existing photo if no new file
            unset($data['photo']);
        }

        return $data;
    }

    /**
     * Hook: delete photo file after court is deleted.
     */
    protected function beforeDelete(int|string $id): void
    {
        $court = $this->repository->find($id);
        if ($court && $court->photo) {
            Storage::disk('public')->delete($court->photo);
        }
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(int $id): Court
    {
        $court = $this->repository->findOrFail($id);
        $court->update(['is_active' => !$court->is_active]);

        return $court->refresh();
    }
}

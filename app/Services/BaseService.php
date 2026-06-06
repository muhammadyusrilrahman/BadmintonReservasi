<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;


abstract class BaseService
{
    /**
     * The repository instance.
     */
    protected BaseRepositoryInterface $repository;

    /**
     * Create a new service instance.
     */
    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all records.
     */
    public function getAll(array $columns = ['*']): Collection
    {
        return $this->repository->all($columns);
    }

    /**
     * Get paginated records.
     */
    public function getPaginated(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $columns);
    }

    /**
     * Find a record by ID.
     */
    public function findById(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->repository->find($id, $columns);
    }

    /**
     * Find a record by ID or throw exception.
     */
    public function findByIdOrFail(int|string $id, array $columns = ['*']): Model
    {
        return $this->repository->findOrFail($id, $columns);
    }

    /**
     * Create a new record with transaction wrapping.
     */
    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $data = $this->beforeCreate($data);
            $model = $this->repository->create($data);
            $this->afterCreate($model, $data);

            return $model;
        });
    }

    /**
     * Update a record with transaction wrapping.
     */
    public function update(int|string $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $data = $this->beforeUpdate($id, $data);
            $model = $this->repository->update($id, $data);
            $this->afterUpdate($model, $data);

            return $model;
        });
    }

    /**
     * Delete a record with transaction wrapping.
     */
    public function delete(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $this->beforeDelete($id);
            $result = $this->repository->delete($id);
            $this->afterDelete($id);

            return $result;
        });
    }

    /**
     * Hook: Before creating a record.
     * Override in child services to modify data before creation.
     */
    protected function beforeCreate(array $data): array
    {
        return $data;
    }

    /**
     * Hook: After creating a record.
     * Override in child services to perform post-creation actions.
     */
    protected function afterCreate(Model $model, array $data): void
    {
        // Override in child service
    }

    /**
     * Hook: Before updating a record.
     * Override in child services to modify data before update.
     */
    protected function beforeUpdate(int|string $id, array $data): array
    {
        return $data;
    }

    /**
     * Hook: After updating a record.
     * Override in child services to perform post-update actions.
     */
    protected function afterUpdate(Model $model, array $data): void
    {
        // Override in child service
    }

    /**
     * Hook: Before deleting a record.
     */
    protected function beforeDelete(int|string $id): void
    {
        // Override in child service
    }

    /**
     * Hook: After deleting a record.
     */
    protected function afterDelete(int|string $id): void
    {
        // Override in child service
    }
}

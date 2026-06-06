<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all records.
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find a record by its primary key.
     */
    public function find(int|string $id, array $columns = ['*']): ?Model;

    /**
     * Find a record by its primary key or throw an exception.
     */
    public function findOrFail(int|string $id, array $columns = ['*']): Model;

    /**
     * Find records by a specific field.
     */
    public function findBy(string $field, mixed $value, array $columns = ['*']): Collection;

    /**
     * Find records matching multiple conditions.
     */
    public function findWhere(array $conditions, array $columns = ['*']): Collection;

    /**
     * Create a new record.
     */
    public function create(array $data): Model;

    /**
     * Update a record by its primary key.
     */
    public function update(int|string $id, array $data): Model;

    /**
     * Delete a record by its primary key.
     */
    public function delete(int|string $id): bool;

    /**
     * Get the count of records.
     */
    public function count(): int;
}

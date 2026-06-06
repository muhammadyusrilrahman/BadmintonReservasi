<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get paginated users for admin index with filters.
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $role = null
    ): LengthAwarePaginator {
        return $this->repository->getPaginatedFiltered($perPage, $search, $role);
    }

    /**
     * Hook: Hash password before creation.
     */
    protected function beforeCreate(array $data): array
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        // Remove role from data to prevent direct assignment if model doesn't handle it
        $this->roleToAssign = $data['role'] ?? null;
        unset($data['role']);

        return $data;
    }

    /**
     * Hook: Hash password if provided, otherwise remove it to prevent overwriting.
     */
    protected function beforeUpdate(int|string $id, array $data): array
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $this->roleToAssign = $data['role'] ?? null;
        unset($data['role']);

        return $data;
    }

    /**
     * Hook: Assign role after creation.
     */
    protected function afterCreate(\Illuminate\Database\Eloquent\Model $model, array $data): void
    {
        if (isset($this->roleToAssign)) {
            $model->assignRole($this->roleToAssign);
        }
    }

    /**
     * Hook: Sync role after update.
     */
    protected function afterUpdate(\Illuminate\Database\Eloquent\Model $model, array $data): void
    {
        if (isset($this->roleToAssign)) {
            $model->syncRoles([$this->roleToAssign]);
        }
    }
}

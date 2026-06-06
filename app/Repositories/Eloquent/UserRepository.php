<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Relations to eager load by default.
     */
    protected array $with = ['roles'];

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get paginated users with search (name, email) and role filters.
     */
    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $role = null
    ): LengthAwarePaginator {
        return $this->model->newQuery()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($q) use ($role) {
                $q->whereHas('roles', fn($r) => $r->where('name', $role));
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * The Eloquent model instance.
     */
    protected Model $model;

    /**
     * Relations to eager load on every query.
     *
     * @var array<string>
     */
    protected array $with = [];

    /**
     * Create a new repository instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get a new query builder with eager loading.
     */
    protected function query()
    {
        return $this->model->newQuery()->with($this->with);
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->query()->get($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->query()->find($id, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail(int|string $id, array $columns = ['*']): Model
    {
        return $this->query()->findOrFail($id, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(string $field, mixed $value, array $columns = ['*']): Collection
    {
        return $this->query()->where($field, $value)->get($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findWhere(array $conditions, array $columns = ['*']): Collection
    {
        return $this->query()->where($conditions)->get($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int|string $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);

        return $record->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);

        return $record->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return $this->model->count();
    }
}

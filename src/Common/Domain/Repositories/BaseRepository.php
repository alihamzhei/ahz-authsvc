<?php

namespace Src\Common\Domain\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Src\Common\Domain\Exceptions\BadRequestException;
use Src\Common\Infrastructure\Repositories\EloquentRepositoryInterface;

abstract class BaseRepository implements EloquentRepositoryInterface
{
    protected Model $model;

    /**
     * @throws BadRequestException
     */
    public function __construct(public Application $app)
    {
        $this->makeModel();
    }

    abstract protected function model();

    /**
     * @throws BadRequestException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new BadRequestException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * all
     *
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query = $query->with($relations);
        }

        return $query->get($columns);
    }

    /**
     * create
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * update
     *
     * @param array $attributes
     * @param int $id
     * @param bool $withTrashed
     * @return Model
     */
    public function update(array $attributes, int $id, bool $withTrashed = false): Model
    {
        $record = $this->find($id, withTrashed: $withTrashed);
        $record->update($attributes);
        return $record;
    }

    /**
     * delete
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    /**
     * forceDelete
     *
     * @param int $id
     * @return bool
     */
    public function forceDelete(int $id): bool
    {
        return $this->find($id)->forceDelete();
    }

    /**
     * find
     *
     * @param int $id
     * @param array|string[] $columns
     * @param bool $withTrashed
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*'], bool $withTrashed = false): ?Model
    {
        if ($withTrashed) {
            return $this->model->withTrashed()->where('id', $id)->first($columns);
        }

        return $this->model->find($id, $columns);
    }

    /**
     * find By
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return Model|null
     */
    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model
    {
        return $this->model->where($field, '=', $value)->first($columns);
    }

    /**
     * find Where
     *
     * @param array $where
     * @param array $columns
     * @return Collection
     */
    public function findWhere(array $where, array $columns = ['*']): Collection
    {
        $query = $this->model->newQuery();

        foreach ($where as $field => $value) {
            $query = $query->where($field, $value);
        }

        return $query->get($columns);
    }

    /**
     * find Where In
     *
     * @param string $field
     * @param array $values
     * @param string[] $columns
     * @return Collection
     */
    public function findWhereIn(string $field, array $values, array $columns = ['*']): Collection
    {
        return $this->model->whereIn($field, $values)->get($columns);
    }

    /**
     * find Where Not In
     *
     * @param string $field
     * @param array $values
     * @param array $columns
     * @return Collection
     */
    public function findWhereNotIn(string $field, array $values, array $columns = ['*']): Collection
    {
        return $this->model->whereNotIn($field, $values)->get($columns);
    }

    /**
     * paginate
     *
     * @param int $perPage
     * @param array|string[] $columns
     * @param array $where
     * @param array $orWhere
     * @param array $orderBy
     * @param bool $withTrashed
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $where = [], array $orWhere = [], array $orderBy = [], bool $withTrashed = false): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if ($withTrashed) {
            $query->withTrashed();
        }

        foreach ($where as $field => $value) {
            if (is_array($value)) {
                $query->where($field, $value[0], $value[1]);
                continue;
            }

            $query->where($field, $value);
        }

        $query->where(function ($query) use ($orWhere) {
            foreach ($orWhere as $field => $value) {
                if (is_array($value)) {
                    $query->orWhere($field, $value[0], $value[1]);
                    continue;
                }
                $query->orWhere($field, $value);
            }
        });

        $orderByColumn = 'created_at';
        $orderByType = 'desc';
        if ($orderBy) {
            $orderByColumn = $orderBy[0] ?? 'created_at';
            $orderByType = $orderBy[1] ?? 'desc';
        }

        return $query->orderBy($orderByColumn, $orderByType)->paginate($perPage, $columns);
    }

    /**
     * with
     *
     * @param array $relations
     * @return Builder
     */
    public function with(array $relations): Builder
    {
        return $this->model->with($relations);
    }

    /**
     * count
     *
     * @param array $where
     * @return int
     */
    public function count(array $where = []): int
    {
        $query = $this->model->newQuery();

        foreach ($where as $field => $value) {
            $query = $query->where($field, $value);
        }

        return $query->count();
    }
}

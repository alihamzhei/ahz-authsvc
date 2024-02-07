<?php

namespace Src\Common\Infrastructure\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []): Collection;

    public function create(array $attributes): Model;

    public function update(array $attributes, int $id, bool $withTrashed = false): Model;

    public function delete(int $id): bool;

    public function forceDelete(int $id): bool;

    public function find(int $id, array $columns = ['*'], bool $withTrashed = false): ?Model;

    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model;

    public function findWhere(array $where, array $columns = ['*']): Collection;

    public function findWhereIn(string $field, array $values, array $columns = ['*']): Collection;

    public function findWhereNotIn(string $field, array $values, array $columns = ['*']): Collection;

    public function paginate(int $perPage = 15, array $columns = ['*'], array $where = [], array $orWhere = [], array $orderBy = [] , bool $withTrashed = false): LengthAwarePaginator;

    public function with(array $relations): Builder;

    public function count(array $where = []): int;
}

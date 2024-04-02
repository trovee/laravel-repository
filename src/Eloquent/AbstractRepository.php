<?php

namespace Trovee\Repository\Eloquent;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Exceptions\NoResultsFoundException;

/**
 * @method ?Model getById(int $id)
 * @method ?Model getByUuid(string $uuid)
 */
abstract class AbstractRepository implements RepositoryInterface
{
    use ForwardsCalls;

    protected string $model;

    protected Builder $query;

    public function getBuilder(): Builder
    {
        if (!isset($this->query)) {
            $this->createNewBuilder();
        }

        return $this->query;
    }

    public function proxyOf(string $model): RepositoryInterface
    {
        $this->model = $model;

        return $this;
    }

    public function createNewBuilder(): RepositoryInterface
    {
        /** @var Model $model */
        $model = app()->make($this->model);
        $this->query = $model->newQuery();

        return $this;
    }

    public function where(array $conditions): RepositoryInterface
    {
        $this->query = $this->getBuilder()->where($conditions);

        return $this;
    }

    public function getByAttributes(array $attributes): Collection
    {
        $this->where($attributes);

        return $this->getBuilder()->get();
    }

    /**
     * @throws NoResultsFoundException
     */
    public function getOrFailByAttributes(array $attributes): Collection
    {
        $result = $this->getByAttributes($attributes);

        if ($result->isEmpty()) {
            throw new NoResultsFoundException($this->model);
        }

        return $result;
    }

    public function firstByAttributes(array $attributes): ?Model
    {
        $this->where($attributes);

        return $this->getBuilder()->first();
    }

    /**
     * @throws NoResultsFoundException
     */
    public function firstOrFailByAttributes(array $attributes): Model
    {
        $result = $this->firstByAttributes($attributes);

        if (is_null($result)) {
            throw new NoResultsFoundException($this->model);
        }

        return $result;
    }

    public function all(): Collection
    {
        return $this->getBuilder()->get();
    }

    public function first(): ?Model
    {
        return $this->getBuilder()->first();
    }

    /**
     * @throws NoResultsFoundException
     */
    public function firstOrFail(): Arrayable
    {
        return $this->firstOrFailByAttributes([]);
    }

    public function __call($method, $parameters)
    {
        return match (true) {
            $this->isCallingExistingMethod($method) => $this->{$method}(...$parameters),
            $this->isCallingGetByColumn($method) => $this->firstByAttributes([
                $this->getColumnNameFromMethod($method, 'getBy') => $parameters[0],
            ]),
            default => $this->forwardCallTo($this->getBuilder(), $method, $parameters),
        };

    }

    protected function isCallingExistingMethod(string $method): bool
    {
        return method_exists($this, $method);
    }

    protected function isCallingSomethingByColumn(string $method, string $prefix): bool
    {
        return Str::startsWith($method, $prefix);
    }

    protected function getColumnNameFromMethod(string $method, string $prefix): string
    {
        return Str::snake(Str::after($method, $prefix));
    }

    protected function isCallingGetByColumn(string $method): bool
    {
        return $this->isCallingSomethingByColumn($method, 'getBy');
    }
}

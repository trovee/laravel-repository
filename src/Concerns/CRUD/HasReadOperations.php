<?php

namespace Trovee\Repository\Concerns\CRUD;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Trovee\Repository\Exceptions\NoResultsFoundException;

trait HasReadOperations
{
    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    public function getByAttributes(array $attributes): Collection
    {
        $this->applyCriteria();
        $this->where($attributes);

        $this->trigger('op:read:getByAttributes', $attributes);

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

        $this->trigger('op:read:getOrFailByAttributes', $attributes);

        return $result;
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    public function firstByAttributes(array $attributes): ?Model
    {
        $this->applyCriteria();
        $this->where($attributes);

        $this->trigger('op:read:firstByAttributes', $attributes);

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

        $this->trigger('op:read:firstOrFailByAttributes', $attributes);

        return $result;
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    public function all(): Collection
    {
        $this->applyCriteria();

        $this->trigger('op:read:all');

        return $this->getBuilder()->get();
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    public function first(): ?Model
    {
        $this->applyCriteria();

        $this->trigger('op:read:first');

        return $this->getBuilder()->first();
    }

    /**
     * @throws NoResultsFoundException
     */
    public function firstOrFail(): Arrayable
    {
        $this->trigger('op:read:firstOrFail');

        return $this->firstOrFailByAttributes([]);
    }
}

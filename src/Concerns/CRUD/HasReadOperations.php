<?php

namespace Trovee\Repository\Concerns\CRUD;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Trovee\Repository\Exceptions\NoResultsFoundException;

trait HasReadOperations
{
    public function getByAttributes(array $attributes): Collection
    {
        $this->applyCriteria();
        $this->where($attributes);

        $this->trigger('op:read:getByAttributes', $attributes);

        $this->setResult($this->getBuilder()->get());

        return $this->result;
    }

    public function getOrFailByAttributes(array $attributes): Collection
    {
        $result = $this->getByAttributes($attributes);

        if ($result->isEmpty()) {
            throw new NoResultsFoundException($this->model);
        }

        $this->trigger('op:read:getOrFailByAttributes', $attributes);
        $this->setResult($result);

        return $this->result;
    }

    public function firstByAttributes(array $attributes): ?Model
    {
        $this->applyCriteria();
        $this->where($attributes);

        $this->trigger('op:read:firstByAttributes', $attributes);
        $this->setResult($this->getBuilder()->first());

        return $this->result;
    }

    public function firstOrFailByAttributes(array $attributes): Model
    {
        $result = $this->firstByAttributes($attributes);

        if (is_null($result)) {
            throw new NoResultsFoundException($this->model);
        }

        $this->trigger('op:read:firstOrFailByAttributes', $attributes);
        $this->setResult($result);

        return $this->result;
    }

    public function all(): Collection
    {
        $this->applyCriteria();

        $this->trigger('op:read:all');

        $this->setResult($this->getBuilder()->get());

        return $this->result;
    }

    public function first(): ?Model
    {
        $this->applyCriteria();

        $this->trigger('op:read:first');
        $this->setResult($this->getBuilder()->first());

        return $this->result;
    }

    public function firstOrFail(): Model
    {
        $this->trigger('op:read:firstOrFail');

        return $this->firstOrFailByAttributes([]);
    }
}

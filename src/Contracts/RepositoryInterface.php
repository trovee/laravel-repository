<?php

namespace Trovee\Repository\Contracts;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Support\Arrayable;

interface RepositoryInterface
{
    public function boot(): void;

    public function proxyOf(string $model): RepositoryInterface;

    public function createNewBuilder(): RepositoryInterface;

    public function getBuilder(): Builder;

    public function where(array $conditions): RepositoryInterface;

    public function getByAttributes(array $attributes): Arrayable;

    public function getOrFailByAttributes(array $attributes): Arrayable;

    public function firstByAttributes(array $attributes): ?Arrayable;

    public function firstOrFailByAttributes(array $attributes): Arrayable;

    public function all(): Arrayable;

    public function first(): ?Arrayable;

    public function firstOrFail(): Arrayable;
}

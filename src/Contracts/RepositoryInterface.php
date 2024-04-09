<?php

namespace Trovee\Repository\Contracts;

use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Http\FormRequest;

interface RepositoryInterface
{
    public function boot(): void;

    public function proxyOf(string $model): RepositoryInterface;

    public function createNewBuilder(): RepositoryInterface;

    public function getBuilder(): Builder;

    public function where(
        array|Closure|Expression|string $column,
        $operator = null,
        $value = null,
        $boolean = 'and'
    ): RepositoryInterface;

    public function getByAttributes(array $attributes): Arrayable;

    public function getOrFailByAttributes(array $attributes): Arrayable;

    public function firstByAttributes(array $attributes): ?Arrayable;

    public function firstOrFailByAttributes(array $attributes): Arrayable;

    public function all(): Arrayable;

    public function first(): ?Arrayable;

    public function firstOrFail(): Arrayable;

    public function apply(Closure $closure): RepositoryInterface;

    public function create(array $data): Arrayable;

    public function createMany(array $data): Arrayable;

    public function createFromRequest(FormRequest $request): Arrayable;

    public function firstOrCreate(array $search, array $create): Arrayable;

    public function updateOrCreate(array $search, array $update): Arrayable;

    public function createThenReturn(array $data): RepositoryInterface;

    public function update(array $data): ?Arrayable;

    public function updateFromRequest(FormRequest $request): ?Arrayable;

    public function updateThenReturn(array $data): RepositoryInterface;

    public function findAndUpdate(array $attributes, array $data): ?Arrayable;

    public function delete(): bool;

    public function forceDelete(): bool;

    public function deleteThenReturn(): RepositoryInterface;

    public function deleteAllDuplicates(array $attributes): int;

    public function deleteDuplicatesAndKeepOne(array $attributes): int;
}

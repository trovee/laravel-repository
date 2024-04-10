<?php

namespace Trovee\Repository\Contracts;

use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Http\FormRequest;
use Laravel\SerializableClosure\SerializableClosure;

interface RepositoryInterface
{
    /**
     * Boot the repository.
     */
    public function boot(): void;

    /**
     * Set the model to be used by the repository.
     */
    public function proxyOf(string $model): RepositoryInterface;

    /**
     * Create a new query builder instance.
     */
    public function createNewBuilder(): RepositoryInterface;

    /**
     * Get the query builder instance.
     */
    public function getBuilder(): Builder;

    /**
     * Add a basic where clause to the query.
     */
    public function where(
        array|Closure|Expression|string $column,
        $operator = null,
        $value = null,
        $boolean = 'and'
    ): RepositoryInterface;

    /**
     * Get a record by its attributes
     */
    public function getByAttributes(array $attributes): Collection;

    /**
     * Get a record by its attributes or fail
     */
    public function getOrFailByAttributes(array $attributes): Collection;

    /**
     * Get the first record by its attributes
     */
    public function firstByAttributes(array $attributes): ?Model;

    /**
     * Get the first record by its attributes or fail
     */
    public function firstOrFailByAttributes(array $attributes): Model;

    /**
     * Get all records
     */
    public function all(): Collection;

    /**
     * Get the first record
     */
    public function first(): ?Model;

    /**
     * Get the first record or fail
     */
    public function firstOrFail(): Model;

    /**
     * Apply a criteria to the query
     *
     * @param  mixed  ...$args
     */
    public function apply(string|CriteriaInterface|Closure|SerializableClosure $criteria, ...$args): RepositoryInterface;

    /**
     * Push a criteria to the repository
     */
    public function pushCriteria(string|CriteriaInterface|Closure|SerializableClosure $criteria): RepositoryInterface;

    /**
     * Ignore a criteria from the repository
     */
    public function ignoreCriteria(string|CriteriaInterface|Closure|SerializableClosure $criteria): RepositoryInterface;

    /**
     * Create a new record
     */
    public function create(array $data): Model|Collection;

    /**
     * Create multiple records
     */
    public function createMany(array $data): Model|Collection;

    /**
     * Create a record from a request
     */
    public function createFromRequest(FormRequest $request): Model;

    /**
     * Create a record if it does not exist
     */
    public function firstOrCreate(array $search, array $create): Model;

    /**
     * Create a record if it does not exist or update it
     */
    public function updateOrCreate(array $search, array $update): Model;

    /**
     * Create a record and return the repository
     */
    public function createThenReturn(array $data): RepositoryInterface;

    /**
     * Update a record
     *
     * @return ?Model
     */
    public function update(array $data): ?Model;

    /**
     * Update a record from a request
     *
     * @return ?Model
     */
    public function updateFromRequest(FormRequest $request): ?Model;

    /**
     * Update a record and return the repository
     */
    public function updateThenReturn(array $data): RepositoryInterface;

    /**
     * Find a record and update it
     *
     * @return ?Model
     */
    public function findAndUpdate(array $attributes, array $data): ?Model;

    /**
     * Delete a record
     */
    public function delete(?Model $model = null): bool;

    /**
     * Force delete a record
     */
    public function forceDelete(?Model $model = null): bool;

    /**
     * Delete a record and return the repository
     */
    public function deleteThenReturn(?Model $model = null): RepositoryInterface;

    /**
     * Delete all duplicate records
     */
    public function deleteAllDuplicates(array $attributes): int;

    /**
     * Delete all duplicate records and keep one
     */
    public function deleteDuplicatesAndKeepOne(array $attributes): int;
}

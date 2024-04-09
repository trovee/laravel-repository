<?php

namespace Trovee\Repository\Eloquent;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Trovee\Repository\Concerns\BootsTraits;
use Trovee\Repository\Concerns\Criteria\AppliesCriteria;
use Trovee\Repository\Concerns\CRUD;
use Trovee\Repository\Concerns\HasEvents;
use Trovee\Repository\Concerns\InteractsWithModel;
use Trovee\Repository\Contracts\RepositoryInterface;

/**
 * @method ?Model getById(int $id)
 * @method ?Model getByUuid(string $uuid)
 */
abstract class AbstractRepository implements RepositoryInterface
{
    use AppliesCriteria;
    use BootsTraits;
    use CRUD\HasCreateOperations;
    use CRUD\HasDeleteOperations;
    use CRUD\HasReadOperations;
    use CRUD\HasUpdateOperations;
    use ForwardsCalls;
    use HasEvents;
    use InteractsWithModel;

    protected string $model;

    protected Builder $query;

    /**
     * @throws BindingResolutionException
     */
    final public function boot(): void
    {
        $this->createNewBuilder();
        $this->bootTraits();

        $this->trigger('boot');

    }

    public function proxyOf(string $model): RepositoryInterface
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @throws BindingResolutionException
     */
    public function getBuilder(): Builder
    {
        if (! isset($this->query)) {
            $this->createNewBuilder();
        }

        return $this->query;
    }

    /**
     * @throws BindingResolutionException
     */
    public function createNewBuilder(): RepositoryInterface
    {
        $this->createNewQueryBuilder();

        if (count($this->appliedCriteria)) {
            $this->clearAppliedCriteria();
        }

        return $this;
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    public function where(
        array|string|Expression|Closure $column,
        $operator = null,
        $value = null,
        $boolean = 'and'
    ): RepositoryInterface {

        $this->apply(
            fn (Builder $builder) => $builder->where($column, $operator, $value, $boolean)
        );

        return $this;
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
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

    protected function isCallingSomethingBy(string $method, string $prefix): bool
    {
        return Str::startsWith($method, $prefix);
    }

    protected function getColumnNameFromMethod(string $method, string $prefix): string
    {
        return Str::snake(Str::after($method, $prefix));
    }

    protected function isCallingGetByColumn(string $method): bool
    {
        $result = $this->isCallingSomethingBy($method, 'getBy');

        if ($result) {
            $this->trigger('dynamic_call:'.$method);
        }

        return $result;
    }

    private function detectMultiLevelArray(array $data): bool
    {
        if (count($data) == count($data, COUNT_RECURSIVE)) {
            return false;
        }

        $keys = array_keys($data[0]);

        foreach ($data as $item) {
            if (! is_array($item)) {
                return false;
            }

            if (count(array_diff($keys, array_keys($item))) > 0) {
                return false;
            }
        }

        return true;
    }
}

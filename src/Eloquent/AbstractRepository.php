<?php

namespace Trovee\Repository\Eloquent;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Trovee\Repository\Concerns\BootsTraits;
use Trovee\Repository\Concerns\Criteria\AppliesCriteria;
use Trovee\Repository\Concerns\CRUD\HasReadOperations;
use Trovee\Repository\Concerns\HasEvents;
use Trovee\Repository\Contracts\RepositoryInterface;

/**
 * @method ?Model getById(int $id)
 * @method ?Model getByUuid(string $uuid)
 */
abstract class AbstractRepository implements RepositoryInterface
{
    use AppliesCriteria;
    use BootsTraits;
    use ForwardsCalls;
    use HasEvents;
    use HasReadOperations;

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
        if (!isset($this->query)) {
            $this->createNewBuilder();
        }

        return $this->query;
    }

    /**
     * @throws BindingResolutionException
     */
    public function createNewBuilder(): RepositoryInterface
    {
        /** @var Model $model */
        $model = app()->make($this->model);
        $this->query = $model->newQuery();

        if (count($this->appliedCriteria)) {
            $this->clearAppliedCriteria();
        }

        return $this;
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     * @todo Improve this method
     */
    public function where(array $conditions): RepositoryInterface
    {
        collect($conditions)
            ->each(fn($value, $key) => $this->apply(
                fn(Builder $builder) => $builder->where($key, $value)
            ));

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

        if($result) {
            $this->trigger('dynamic_call:'.$method);
        }

        return $result;
    }
}

<?php

namespace Trovee\Repository\Managers;

use Illuminate\Support\Traits\ForwardsCalls;
use ReflectionException;
use Throwable;
use Trovee\Repository\Attributes\Repository;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Exceptions\ClassException;

class RepositoryManager
{
    use ForwardsCalls;

    public function __construct(protected RegistryManager $registryManager)
    {
    }

    /**
     * @throws ClassException
     * @throws ReflectionException
     * @throws Throwable
     */
    public function get(string $model): RepositoryInterface
    {
        // look for the given model has Trovee\Repository\Attributes\Repository attribute
        $repository = $this->registryManager->resolveRepositoryAttribute($model);

        if (! is_null($repository)) {
            return $repository;
        }

        // if not, create a default repository instance
        return $this->registryManager->getDefaultRepositoryAsTargetedToModel($model);
    }

    public function __call(string $name, array $arguments)
    {
        return match (true) {
            method_exists($this->registryManager, $name) => $this->forwardCallTo($this->registryManager, $name, $arguments),
            default => $this->$name(...$arguments),
        };
    }
}

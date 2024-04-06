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
        $repository = $this->registryManager->resolveRepositoryAttribute($model)
            ?? $this->registryManager->getDefaultRepositoryAsTargetedToModel($model);

        if (! ($repository instanceof RepositoryInterface)) {
            throw new RepositoryIntegrityException(
                action: 'boot',
                fqcn: get_class($repository),
                verb: 'implement',
                inheritance: RepositoryInterface::class
            );
        }

        $repository->boot();

        return $repository;
    }

    public function __call(string $name, array $arguments)
    {
        return match (true) {
            method_exists($this->registryManager, $name) => $this->forwardCallTo($this->registryManager, $name, $arguments),
            default => $this->$name(...$arguments),
        };
    }
}

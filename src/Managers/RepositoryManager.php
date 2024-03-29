<?php

namespace Trovee\Repository\Managers;

use Trovee\Repository\Attributes\Repository;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Exceptions\ClassException;

class RepositoryManager
{
    public function __construct(protected RegistryManager $registryManager)
    {
    }

    /**
     * @throws ClassException
     */
    public function get(string $model): RepositoryInterface
    {
        // look for the given model has Trovee\Repository\Attributes\Repository attribute
        // if not, look for the repository in the registry
        // if not, create a default repository instance
        return $this->registryManager->getDefaultRepositoryAsTargetedToModel($model); // this is dummy return
    }
}

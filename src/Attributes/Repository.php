<?php

namespace Trovee\Repository\Attributes;

use Attribute;
use Illuminate\Contracts\Container\BindingResolutionException;
use Throwable;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Exceptions\ClassException;
use Trovee\Repository\Exceptions\ClassNotFoundException;
use Trovee\Repository\Exceptions\RepositoryIntegrityException;
use Trovee\Repository\Facades\Repository as RepositoryFacade;

#[Attribute(Attribute::TARGET_CLASS)]
class Repository
{
    public function __construct(protected string $repositoryFqcn)
    {
    }

    /**
     * @throws Throwable
     * @throws ClassException
     */
    public function getRepository(string $target): RepositoryInterface
    {
        $this->validateRepository();

        try {
            return app($this->repositoryFqcn)->proxyOf($target);
        } catch (BindingResolutionException) {
            return RepositoryFacade::getDefaultRepositoryAsTargetedToModel($target);
        }
    }

    /**
     * @throws ClassNotFoundException
     */
    protected function validateRepository(): void
    {
        $exception = match (true) {
            !interface_exists($this->repositoryFqcn) => new ClassNotFoundException($this->repositoryFqcn),
            !is_subclass_of($this->repositoryFqcn, RepositoryInterface::class) =>
            new RepositoryIntegrityException(
                action: 'validate',
                fqcn: $this->repositoryFqcn,
                verb: 'implements',
                inheritance: RepositoryInterface::class
            ),
            default => null,
        };

        if (!is_null($exception)) {
            throw $exception;
        }

    }
}

<?php

namespace Trovee\Repository\Managers;

use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Throwable;
use Trovee\Repository\Attributes\Repository as RepositoryAttribute;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Exceptions\ClassException;
use Trovee\Repository\Exceptions\ClassNotFoundException;
use Trovee\Repository\Exceptions\RepositoryIntegrityException;

class RegistryManager
{
    protected Repository $config;

    public function __construct(protected Application $app)
    {
        $this->config = new Repository(config('repository'));
    }

    public function boot(): void
    {
    }

    /**
     * @throws ClassException
     */
    public function getDefaultRepositoryAsTargetedToModel(string $model)
    {
        if (!class_exists($model)) {
            throw new ClassNotFoundException($model);
        }

        if (!is_subclass_of($model, Model::class)) {
            throw new RepositoryIntegrityException(
                fqcn: $model,
                verb: 'extends',
                inheritance: Model::class
            );
        }

        $repository = $this->getDefaultRepository();

        return $repository->proxyOf($model);
    }

    /**
     * @throws ReflectionException
     * @throws Throwable
     */
    public function resolveRepositoryAttribute(string $model): ?RepositoryInterface
    {
        if(!class_exists($model)){
            return null;
        }

        $reflection = new ReflectionClass($model);

        if ($reflection->isAbstract()) {
            return null;
        }

        /** @var ?ReflectionAttribute $attribute */
        $attribute = collect($reflection->getAttributes(RepositoryAttribute::class))->first();

        if (is_null($attribute)) {
            return null;
        }

        /** @var RepositoryAttribute $repositoryAttribute */
        $repositoryAttribute = $attribute->newInstance();

        return $repositoryAttribute->getRepository($model);
    }

    public function getDefaultRepository(): RepositoryInterface
    {
        $repository = $this->config->get('default_repository');

        if (is_subclass_of($repository, RepositoryInterface::class)) {
            return $this->app->make($this->config->get('default_repository'));
        }

        throw new RepositoryIntegrityException(
            fqcn: $this->config->get('default_repository'),
            verb: 'implements',
            inheritance: RepositoryInterface::class
        );

    }
}

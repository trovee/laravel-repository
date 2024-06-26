<?php

namespace Trovee\Repository\Managers;

use Illuminate\Config\Repository as Config;
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

class RepositoryManager
{
    protected Config $config;

    public function __construct(protected Application $app)
    {
        $this->config = new Config(config('repository'));
    }

    public function getDefaultRepositoryAsTargetedToModel(string $model)
    {
        if (! class_exists($model)) {
            throw new ClassNotFoundException($model);
        }

        if (! is_subclass_of($model, Model::class)) {
            throw new RepositoryIntegrityException(
                fqcn: $model,
                verb: 'extends',
                inheritance: Model::class
            );
        }

        $repository = $this->getDefaultRepository();

        return $repository->proxyOf($model);
    }

    public function resolveRepositoryAttribute(string $model): ?RepositoryInterface
    {
        if (! class_exists($model)) {
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

        $repositoryAttribute = $attribute->newInstance();

        if (! ($repositoryAttribute instanceof RepositoryAttribute)) {
            throw new RepositoryIntegrityException(
                fqcn: get_class($repositoryAttribute),
                verb: 'be an instance of',
                inheritance: RepositoryAttribute::class
            );
        }

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

    /**
     * @throws ClassException
     * @throws ReflectionException
     * @throws Throwable
     */
    public function get(string $model): RepositoryInterface
    {
        $repository = $this->resolveRepositoryAttribute($model)
            ?? $this->getDefaultRepositoryAsTargetedToModel($model);

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
}

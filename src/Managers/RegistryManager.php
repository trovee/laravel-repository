<?php

namespace Trovee\Repository\Managers;

use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Exceptions\ClassException;

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
            throw ClassException::doesNotExists($model);
        }

        if (!is_subclass_of($model, Model::class)) {
            throw ClassException::isNotModel($model);
        }

        $repository = $this->app->make($this->config->get('default_repository'));

        if ($repository instanceof RepositoryInterface) {
            return $repository->proxyOf($model);
        }

        throw ClassException::isNotRepository($repository);
    }

}

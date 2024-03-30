<?php

namespace Trovee\Repository\Facades;

use Illuminate\Support\Facades\Facade;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Managers\RepositoryManager;

/**
 * @see RepositoryManager
 *
 * @method static RepositoryInterface get(string $model)
 * @method static RepositoryInterface getDefaultRepositoryAsTargetedToModel(string $model)
 */
class Repository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RepositoryManager::class;
    }
}

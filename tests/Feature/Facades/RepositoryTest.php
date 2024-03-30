<?php

use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Facades\Repository;
use Trovee\Repository\Managers\RepositoryManager;
use Workbench\App\Models\User;

it('can resolve the facade as RepositoryManager instance', function () {
    $repository = Repository::getFacadeRoot();
    expect($repository)->toBeInstanceOf(RepositoryManager::class);
});

it('can get UserRepository', function () {

    $repository = Repository::get(User::class);

    expect($repository)->not->toBeNull()->toBeInstanceOf(RepositoryInterface::class);
});

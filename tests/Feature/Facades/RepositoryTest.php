<?php

use Trovee\Repository\Facades\Repository;
use Trovee\Repository\Managers\RepositoryManager;

it('can resolve the facade as RepositoryManager instance', function () {
    $repository = Repository::getFacadeRoot();
    expect($repository)->toBeInstanceOf(RepositoryManager::class);
});

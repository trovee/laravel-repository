<?php

use Mockery as m;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Managers\RegistryManager;
use Trovee\Repository\Managers\RepositoryManager;

beforeEach(function () {
    $this->registryManager = m::mock(RegistryManager::class);
    $this->repositoryManager = new RepositoryManager($this->registryManager);
});

it('gets a repository for a model', function () {
    $model = \Workbench\App\Models\User::class;
    $repositoryMock = m::mock(RepositoryInterface::class);

    $this->registryManager->shouldReceive('resolveRepositoryAttribute')
        ->with($model)
        ->andReturn(null);

    $this->registryManager->shouldReceive('getDefaultRepositoryAsTargetedToModel')
        ->with($model)
        ->andReturn($repositoryMock);

    $repositoryMock->shouldReceive('boot')->once();

    $repository = $this->repositoryManager->get($model);

    expect($repository)->toBe($repositoryMock);
});

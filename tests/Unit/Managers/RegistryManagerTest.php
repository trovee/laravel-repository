<?php

use Illuminate\Database\Eloquent\Model;
use Mockery as m;
use Trovee\Repository\Attributes\Repository as RepositoryAttribute;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Exceptions\RepositoryIntegrityException;
use Trovee\Repository\Managers\RegistryManager;

beforeEach(function () {
    $this->mockApp = m::mock($this->app);
    $this->registryManager = new RegistryManager($this->mockApp);
    $this->configMock = m::mock('config');
    $this->mockApp->shouldReceive('config')->andReturn($this->configMock);
});

it('gets default repository as targeted to model', function () {
    $model = Model::class;
    $repositoryClass = RepositoryInterface::class;
    $repositoryInstance = m::mock($repositoryClass);

    $this->configMock->shouldReceive('get')->with('repository.default_repository')->andReturn($repositoryClass);
    $this->mockApp->shouldReceive('make')->with($repositoryClass)->andReturn($repositoryInstance);
    $repositoryInstance->shouldReceive('proxyOf')->with($model)->andReturn($repositoryInstance);

    $result = $this->registryManager->getDefaultRepositoryAsTargetedToModel($model);

    expect($result)->toBeInstanceOf($repositoryClass);
})->throws(RepositoryIntegrityException::class);

it('resolves repository attribute', function () {
    $model = Model::class;
    $repositoryClass = RepositoryInterface::class;

    $attributeMock = m::mock(ReflectionAttribute::class);
    $reflectionClassMock = m::mock(ReflectionClass::class, [$model]);
    $repositoryAttributeInstance = m::mock(RepositoryAttribute::class);

    $reflectionClassMock->shouldReceive('isAbstract')->andReturn(false);
    $reflectionClassMock->shouldReceive('getAttributes')->with(RepositoryAttribute::class)->andReturn([$attributeMock]);
    $attributeMock->shouldReceive('newInstance')->andReturn($repositoryAttributeInstance);
    $repositoryAttributeInstance->shouldReceive('getRepository')->with($model)->andReturn($repositoryClass);

    $result = $this->registryManager->resolveRepositoryAttribute($model);

    expect($result)->toBe(null);
});

it('gets default repository', function () {
    $repositoryClass = \Trovee\Repository\Eloquent\DefaultRepository::class;
    $repositoryInstance = m::mock($repositoryClass);

    $this->configMock->shouldReceive('get')->with('repository.default_repository')->andReturn($repositoryClass);
    $this->mockApp->shouldReceive('make')->with($repositoryClass)->andReturn($repositoryInstance);

    $result = $this->registryManager->getDefaultRepository();

    expect($result)->toBeInstanceOf($repositoryClass);
});

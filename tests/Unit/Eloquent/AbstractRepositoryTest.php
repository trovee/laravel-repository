<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Mockery as m;
use Trovee\Repository\Eloquent\AbstractRepository;

beforeEach(function () {
    $this->repository = m::mock(AbstractRepository::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial()
        ->proxyOf(\Workbench\App\Models\User::class);

    $this->model = m::mock(Model::class);
    $this->builder = m::mock(Builder::class);

    App::shouldReceive('make')
        ->with(\Workbench\App\Models\User::class)
        ->andReturn($this->model);

    $this->model->shouldReceive('newQuery')->andReturn($this->builder);
    $this->repository->shouldReceive('getBuilder')->andReturn($this->builder);

});

it('can boot the repository', function () {
    $this->repository->shouldReceive('trigger')->once()->with('boot');
    $this->repository->boot();

    expect($this->repository->getBuilder())->toBe($this->builder);
});

it('can proxy to a model', function () {
    $result = $this->repository->proxyOf('Model');

    expect($result)->toBe($this->repository);
});

it('can create a new query builder', function () {
    $result = $this->repository->createNewBuilder();

    expect($result)->toBe($this->repository)
        ->and($this->repository->getBuilder())->toBe($this->builder);
});

it('forwards call to the query builder for undefined methods', function () {
    $methodName = 'orderBy';
    $parameters = ['name', 'asc'];

    $this->builder->shouldReceive($methodName)->with(...$parameters)->once();

    $this->repository->{$methodName}(...$parameters);
});

it('can handle dynamic get by column methods', function () {
    $uuid = '1234';
    $this->repository->shouldReceive('firstByAttributes')
        ->once()
        ->with(['uuid' => $uuid])
        ->andReturn($this->model);

    $result = $this->repository->getByUuid($uuid);

    expect($result)->toBe($this->model);
});

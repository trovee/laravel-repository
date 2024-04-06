<?php

use Illuminate\Database\Eloquent\Builder;
use Trovee\Repository\Eloquent\DefaultRepository;
use Workbench\App\Models\User;
use Workbench\Database\Factories\UserFactory;

it('can be the proxy for a model', function () {

    $repository = new DefaultRepository();
    $repository->proxyOf(User::class);

    UserFactory::new()->create();

    expect($repository->first())
        ->toBeInstanceOf(User::class);
});

it('can get the model by id', function () {
    $repository = new DefaultRepository();
    $repository->proxyOf(User::class);

    $user = UserFactory::new(['id' => 1])->create();

    expect($repository->getById(1))
        ->toBeInstanceOf(get_class($user))
        ->and(array_diff($repository->getById(1)->toArray(), $user->toArray()))
        ->toBe([]);
});

it('can apply an anonymous criteria', function () {
    $repository = new DefaultRepository();
    $repository->proxyOf(User::class);

    $user = UserFactory::new(['id' => 1])->create();

    $repository->apply(function (Builder $query): Builder {
        return $query->where('id', 1);
    });

    expect($repository->first())
        ->toBeInstanceOf(get_class($user))
        ->and(array_diff($repository->first()->toArray(), $user->toArray()))
        ->toBe([]);
});

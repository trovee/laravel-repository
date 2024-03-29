<?php

it('can find all repository candidates', function () {
    $registry = app('repository.registry');

    $invasion = invade($registry, fn() => $this->candidates, return: true);

    expect($invasion)->toBeArray()
        ->toHaveCountGreaterThan(0);
});

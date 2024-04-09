<?php

namespace Trovee\Repository\Contracts;

interface HookInterface
{
    public function onTrigger(RepositoryInterface $repository, ...$args): void;
}

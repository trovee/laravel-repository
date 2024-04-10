<?php

namespace Trovee\Repository\Contracts;

interface HookInterface
{
    /**
     * Define the functionality when the hook is triggered.
     */
    public function onTrigger(RepositoryInterface $repository, ...$args): void;
}

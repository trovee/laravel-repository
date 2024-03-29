<?php

namespace Trovee\Repository\Contracts;

interface RepositoryInterface
{
    public function proxyOf(string $model);
}

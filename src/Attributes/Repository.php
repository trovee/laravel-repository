<?php

namespace Trovee\Repository\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Repository
{

    public function __construct(protected string $repositoryFqcn)
    {
    }

}

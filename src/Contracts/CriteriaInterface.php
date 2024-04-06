<?php

namespace Trovee\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface CriteriaInterface
{
    public function apply(Builder $query): Builder;
}

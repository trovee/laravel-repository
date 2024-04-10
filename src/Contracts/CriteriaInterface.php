<?php

namespace Trovee\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface CriteriaInterface
{
    /**
     * Apply the criteria to the query.
     */
    public function apply(Builder $query): Builder;
}

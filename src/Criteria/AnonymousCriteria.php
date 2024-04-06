<?php

namespace Trovee\Repository\Criteria;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Laravel\SerializableClosure\SerializableClosure;
use Trovee\Repository\Contracts\CriteriaInterface;

class AnonymousCriteria implements CriteriaInterface
{
    protected SerializableClosure $closure;

    public function __construct(Closure|SerializableClosure $closure, protected array $args = [])
    {
        $this->closure = $closure instanceof Closure
            ? new SerializableClosure($closure)
            : $closure;
    }

    public function apply(Builder $query): Builder
    {
        return $this->closure->getClosure()($query, ...$this->args);
    }
}

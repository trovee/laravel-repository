<?php

namespace Trovee\Repository\Concerns\Criteria;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;
use Trovee\Repository\Contracts\CriteriaInterface;
use Trovee\Repository\Criteria\AnonymousCriteria;

trait AppliesCriteria
{
    protected array $criteria = [];

    protected array $appliedCriteria = [];

    final protected function bootAppliesCriteria(): void
    {
        $this->criteria = collect($this->criteria)
            ->map(fn($criteria) => $this->resolveCriteria($criteria))
            ->toArray();
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    final protected function applyCriteria(): void
    {
        foreach ($this->criteria as $criteria) {
            if ($this->criteriaApplied($criteria)) {
                continue;
            }

            $this->apply($criteria);
        }
    }

    final public function criteriaApplied(CriteriaInterface $criteria): bool
    {
        return isset($this->appliedCriteria[$this->hashCriteria($criteria)]);
    }

    final protected function hashCriteria(CriteriaInterface $criteria): string
    {
        return base64_encode(md5(serialize($criteria)));
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    final public function pushCriteria(string|CriteriaInterface|Closure|SerializableClosure $criteria): static
    {
        $this->criteria[] = $this->resolveCriteria($criteria);

        return $this;
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    final public function resolveCriteria(
        string|CriteriaInterface|Closure|SerializableClosure $criteria,
        ...$args
    ): CriteriaInterface|SerializableClosure {
        return match (true) {
            is_string($criteria) => app()->make($criteria, $args),
            $criteria instanceof Closure,
                $criteria instanceof SerializableClosure => new AnonymousCriteria($criteria, $args),
            $criteria instanceof CriteriaInterface => $criteria,
        };
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    final public function apply(string|CriteriaInterface|Closure|SerializableClosure $criteria, ...$args): static
    {
        $criteria = $this->resolveCriteria($criteria, ...$args);

        $this->query = $criteria->apply($this->getBuilder());

        $this->appliedCriteria[$this->hashCriteria($criteria)] = $criteria;

        return $this;
    }

    final protected function clearAppliedCriteria(): void
    {
        $this->appliedCriteria = [];
    }
}

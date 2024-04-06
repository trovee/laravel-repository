<?php

namespace Trovee\Repository\Concerns\Criteria;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;
use Trovee\Repository\Contracts\CriteriaInterface;

trait AppliesCriteria
{
    protected array $criteria = [];

    protected array $appliedCriteria = [];

    protected function bootAppliesCriteria(): void
    {
        $this->criteria = collect($this->criteria)
            ->map(fn ($criteria) => $this->resolveCriteria($criteria))
            ->toArray();
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    protected function applyCriteria(): void
    {
        foreach ($this->criteria as $criteria) {
            if ($this->criteriaApplied($criteria)) {
                continue;
            }

            $this->apply($criteria);
        }
    }

    protected function criteriaApplied(CriteriaInterface|SerializableClosure $criteria): bool
    {
        return isset($this->appliedCriteria[$this->hashCriteria($criteria)]);
    }

    protected function hashCriteria(CriteriaInterface|SerializableClosure $criteria): string
    {
        return base64_encode(md5(serialize($criteria)));
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    public function pushCriteria(string|CriteriaInterface|Closure|SerializableClosure $criteria): static
    {
        $this->criteria[] = $this->resolveCriteria($criteria);

        return $this;
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    public function resolveCriteria(
        string|CriteriaInterface|Closure|SerializableClosure $criteria
    ): CriteriaInterface|SerializableClosure {
        return match (true) {
            is_string($criteria) => app()->make($criteria),
            $criteria instanceof Closure => new SerializableClosure($criteria),
            $criteria instanceof CriteriaInterface,
            $criteria instanceof SerializableClosure => $criteria,
        };
    }

    /**
     * @throws BindingResolutionException
     * @throws PhpVersionNotSupportedException
     */
    public function apply(string|CriteriaInterface|Closure|SerializableClosure $criteria): static
    {
        $criteria = $this->resolveCriteria($criteria);

        $this->query = match (true) {
            $criteria instanceof CriteriaInterface => $criteria->apply($this->getBuilder()),
            $criteria instanceof SerializableClosure => value($criteria->getClosure(), $this->getBuilder()),
        };

        $this->appliedCriteria[$this->hashCriteria($criteria)] = $criteria;

        return $this;
    }

    private function clearAppliedCriteria()
    {
        $this->appliedCriteria = [];
    }
}

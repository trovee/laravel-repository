<?php

namespace Trovee\Repository\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Trovee\Repository\Contracts\RepositoryInterface;

trait InteractsWithModel
{
    protected Model $record;

    protected Model|Collection $result;

    public function getModelInstance(): Model
    {
        if (isset($this->record)) {
            return $this->record;
        }

        return app()->make($this->model);
    }

    protected function setRecord(Model $record): RepositoryInterface
    {
        $this->record = $record;

        return $this;
    }

    protected function setResult(Model|Collection $result): RepositoryInterface
    {
        $this->result = $result;

        return $this;
    }

    protected function getRecord(): Model
    {
        if (! isset($this->record)) {
            if (isset($this->result) && $this->result instanceof Model) {
                $this->setRecord($this->result);

                return $this->record;
            }

            throw new \LogicException(
                'Failed to call getRecord() method. '.static::class.' needs to know which record to get.'
            );
        }

        return $this->record;
    }

    protected function getResult(): Model|Collection
    {
        return $this->result;
    }

    public function createNewQueryBuilder(): Builder
    {
        $this->query = $this->getModelInstance()->newQuery();

        return $this->query;
    }

    public function getFillable(): array
    {
        return $this->getModelInstance()->getFillable();
    }

    public function filterFillable(array $data): array
    {
        return collect($data)->only($this->getFillable())->toArray();
    }

    public function getTable(): string
    {
        return $this->getModelInstance()->getTable();
    }

    public function getKeyName(): string
    {
        return $this->getModelInstance()->getKeyName();
    }

    public function getQualifiedKeyName(): string
    {
        return $this->getModelInstance()->qualifyColumn($this->getKeyName());
    }

    public function getKey(): mixed
    {
        return $this->getModelInstance()->getKey();
    }
}

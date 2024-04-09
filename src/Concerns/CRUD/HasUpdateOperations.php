<?php

namespace Trovee\Repository\Concerns\CRUD;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Trovee\Repository\Contracts\RepositoryInterface;

trait HasUpdateOperations
{
    protected function getUpdatable(): Model
    {
        if (isset($this->result) && $this->result instanceof Model) {
            return $this->result;
        }

        if (isset($this->record)) {
            return $this->record;
        }

        throw new \LogicException(
            'Failed to call update() method. '.static::class.' needs to know which record to update.'
        );
    }

    public function update(array $data): ?Model
    {
        $data = $this->filterFillable($data);

        $updatable = $this->getUpdatable();

        $this->trigger('updating', $old = $updatable->toArray(), $data);
        $updated = $this->getRecord()->update($data);

        if (! $updated) {
            $this->trigger('update:failed', $old, $data);

            return null;
        }

        $this->trigger('update:success', $old, $data);

        $this->setRecord($this->getRecord()->fresh());

        return $this->record;
    }

    public function updateThenReturn(array $data): RepositoryInterface
    {
        $this->update($data);

        return $this;
    }

    public function findAndUpdate(array $attributes, array $data): ?Model
    {
        $updatable = $this->firstOrFailByAttributes($attributes);

        $this->setResult($updatable);

        return $this->update($data);
    }

    public function updateFromRequest(FormRequest $request): ?Model
    {
        return $this->update($request->validated());
    }
}

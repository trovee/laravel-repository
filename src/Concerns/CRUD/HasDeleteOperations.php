<?php

namespace Trovee\Repository\Concerns\CRUD;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasDeleteOperations
{
    protected function modelUsingSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses($this->getModelInstance()));
    }

    public function delete(?Model $model = null): bool
    {
        if (! is_null($model)) {
            $this->setRecord($model);
        }

        $this->trigger('deleting', $record = $this->getRecord());

        $result = $this->modelUsingSoftDeletes()
            ? $record->delete()
            : $this->forceDelete();

        $this->trigger('deleted', $this->getRecord());

        return $result;
    }

    public function forceDelete(?Model $model = null): bool
    {
        if (! is_null($model)) {
            $this->setRecord($model);
        }

        $this->trigger('force_deleting', $record = $this->getRecord());

        $result = $record->forceDelete();

        $this->trigger('force_deleted', $record);

        return $result;
    }

    public function deleteThenReturn(?Model $model = null): self
    {
        $this->delete($model);

        return $this;
    }

    /**
     * @return int Number of deleted records
     */
    public function deleteAllDuplicates(array $attributes): int
    {
        $this->trigger('deleting_duplicates', $attributes);

        $result = $this->createNewQueryBuilder()
            ->where($attributes)
            ->delete();

        $this->trigger('deleted_duplicates', $attributes);

        return $result;
    }

    /**
     * @return int Number of deleted records
     */
    public function deleteDuplicatesAndKeepOne(array $attributes): int
    {
        $this->trigger('deleting_duplicates_and_keeping_one', $attributes);

        $result = $this->createNewQueryBuilder()
            ->where($attributes)
            ->orderBy('id', 'desc')
            ->offset(1)
            ->delete();

        $this->trigger('deleted_duplicates_and_kept_one', $attributes);

        return $result;
    }
}

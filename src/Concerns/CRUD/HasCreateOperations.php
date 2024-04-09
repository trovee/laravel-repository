<?php

namespace Trovee\Repository\Concerns\CRUD;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Trovee\Repository\Contracts\RepositoryInterface;

trait HasCreateOperations
{
    public function create(array $data): Model|Collection
    {
        if ($this->detectMultiLevelArray($data)) {
            return $this->createMany($data);
        }

        $data = $this->filterFillable($data);

        $this->trigger('creating', $data);
        $newRecord = $this->createNewQueryBuilder()->create($data);
        $this->setRecord($newRecord);
        $this->trigger('created', $data);

        return $this->record;
    }

    public function createFromRequest(FormRequest $request): Model
    {
        return $this->create($request->validated());
    }

    public function firstOrCreate(array $search, array $create): Model
    {
        $this->setResult(
            $this->createNewQueryBuilder()->firstOrCreate($search, $create)
        );

        return $this->result;
    }

    public function updateOrCreate(array $search, array $update): Model
    {
        $this->setResult($this->createNewQueryBuilder()->updateOrCreate($search, $update));

        return $this->result;
    }

    public function createThenReturn(array $data): RepositoryInterface
    {
        $this->create($data);

        return $this;
    }

    public function createMany(array $data): Collection
    {
        $records = [];
        foreach ($data as $item) {
            $records[] = $this->create($item);
        }

        $this->setResult(new Collection($records));

        return $this->result;
    }
}

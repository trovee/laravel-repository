<?php

namespace Trovee\Repository\Events;

use Illuminate\Contracts\Support\Arrayable;

class MemoryFragment implements Arrayable
{
    public function __construct(
        protected string $event,
        protected mixed $data
    ) {
    }

    public function toArray()
    {
        return [
            'event' => $this->event,
            'data' => $this->data,
        ];
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function setEvent(string $event): MemoryFragment
    {
        $this->event = $event;

        return $this;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): MemoryFragment
    {
        $this->data = $data;

        return $this;
    }
}

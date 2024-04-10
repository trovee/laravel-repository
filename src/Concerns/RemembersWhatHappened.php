<?php

namespace Trovee\Repository\Concerns;

use Illuminate\Support\Collection;
use Trovee\Repository\Events\MemoryFragment;

trait RemembersWhatHappened
{
    protected Collection $history;

    public function bootRemembersWhatHappened(): void
    {
        if (! $this->isHistoryEnabled()) {
            return;
        }

        $this->history = new Collection();
    }

    public function remember(string $event, mixed $data): void
    {
        if (! $this->isHistoryEnabled()) {
            return;
        }

        $this->optimizeMemory();

        $this->history->put(now()->toString(), new MemoryFragment($event, $data));
    }

    public function isHappened(string $event): bool
    {
        if (! $this->isHistoryEnabled()) {
            return false;
        }

        return $this->history->contains('event', $event);
    }

    public function isHistoryEnabled(): bool
    {
        return config('repository.history.enabled');
    }

    public function optimizeMemory(): void
    {
        if ($this->history->count() <= config('repository.history.max_event_to_remember')) {
            return;
        }
        /** @var MemoryFragment $oldest */
        $oldest = $this->history->first();
        if ($oldest->getEvent() !== 'boot') {
            $this->history->shift();

            return;
        }

        $this->history->splice(1, 1);
    }
}

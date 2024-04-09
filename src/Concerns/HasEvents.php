<?php

namespace Trovee\Repository\Concerns;

use Closure;
use Illuminate\Support\Str;
use Trovee\Repository\Contracts\HookInterface;
use Trovee\Repository\Contracts\RepositoryInterface;
use Trovee\Repository\Exceptions\RepositoryIntegrityException;

trait HasEvents
{
    /**
     * @var array<string, array<int,string|Closure>>
     */
    protected array $hooks = [];

    protected function triggerHook(string $hook, ...$args): void
    {
        $this->triggerSelfRegistryHooks($hook, ...$args);

        $this->triggerMethodHook($hook, $args);

        $this->triggerClassHook($hook, ...$args);

    }

    private function triggerMethodHook(string $hook, array $args): void
    {
        $hookMethod = 'on'.Str::studly($hook);

        if (method_exists($this, $hookMethod)) {
            $this->{$hookMethod}($this, ...$args);
        }
    }

    private function triggerClassHook(string $hook, ...$args): void
    {
        if (class_exists($hook)) {
            $hook = app()->make($hook, ['repository' => $this, ...$args]);

            if (! ($hook instanceof HookInterface)) {
                throw new RepositoryIntegrityException(
                    action: 'trigger hook',
                    fqcn: $hook,
                    verb: 'implement',
                    inheritance: HookInterface::class
                );
            }

            $hook->onTrigger($this, ...$args);
        }
    }

    private function triggerSelfRegistryHooks(string $hook, ...$args): void
    {
        foreach ($this->getTriggerableHooks($hook) as $listener) {
            $this->triggerClassHook($listener, ...$args);
        }
    }

    private function addHook(string $hook, string|Closure $fqcn): RepositoryInterface
    {
        $this->hooks[$hook][] = $fqcn;

        return $this;
    }

    public function addHooks(array $hooks): RepositoryInterface
    {
        foreach ($hooks as $hook => $listeners) {
            foreach ($listeners as $fqcn) {
                $this->addHook($hook, $fqcn);
            }
        }

        return $this;
    }

    public function getTriggerableHooks(?string $hook = null): array
    {
        return $this->hooks[$hook] ?? [];
    }

    public function getHooks(): array
    {
        return $this->hooks;
    }

    protected function dispatchEvent(string $event, ...$args): void
    {
        $args['repository'] = $this;

        event($event, $args);
    }
}

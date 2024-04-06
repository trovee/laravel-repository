<?php

namespace Trovee\Repository\Concerns;

trait BootsTraits
{
    protected function bootTraits(): void
    {
        $class = static::class;

        $booted = [];

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'boot'.class_basename($trait);

            if (method_exists($class, $method) && ! in_array($method, $booted)) {
                $this->{$method}();
                $booted[] = $method;
            }
        }
    }
}

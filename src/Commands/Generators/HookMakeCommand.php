<?php

namespace Trovee\Repository\Commands\Generators;

use Illuminate\Console\GeneratorCommand;

class HookMakeCommand extends GeneratorCommand
{
    protected $name = 'make:hook';

    protected $description = 'Create a new Hook class';

    protected $type = 'Hook';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/hook.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Hooks';
    }
}

<?php

namespace Trovee\Repository\Commands\Generators;

use Illuminate\Console\GeneratorCommand;

class RepositoryInterfaceMakeCommand extends GeneratorCommand
{
    protected $name = 'make:repository-interface';

    protected $description = 'Create a new repository interface';

    protected $type = 'Repository';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/repository.interface.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories';
    }
}

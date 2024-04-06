<?php

namespace Trovee\Repository\Commands;

use Illuminate\Console\GeneratorCommand;

class CriteriaMakeCommand extends GeneratorCommand
{
    protected $name = 'make:criteria';

    protected $description = 'Create a new Criteria class';

    protected $type = 'Criteria';

    /**
     * {@inheritDoc}
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/criteria.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Criteria';
    }
}

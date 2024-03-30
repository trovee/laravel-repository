<?php

namespace Trovee\Repository\Commands\Generators;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class EloquentRepositoryMakeCommand extends GeneratorCommand
{
    protected $name = 'make:repository-eloquent';

    protected $description = 'Create a new eloquent repository implementation';

    protected $type = 'Repository Implementation for Eloquent';

    protected function getStub()
    {
        return __DIR__.'/../../../stubs/repository.eloquent.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories\Eloquent';
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceModel($stub, $name)
            ->replaceInterface($stub, $name)
            ->replaceClass($stub, $name);
    }

    protected function replaceInterface(&$stub, $name)
    {
        $interface = str_replace('Eloquent\\', 'Contracts\\', $name).'Contract';
        $interfaceShortName = class_basename($interface);
        $stub = str_replace('{{ interface }}', $interface, $stub);
        $stub = str_replace('{{ interfaceShortName }}', $interfaceShortName, $stub);

        return $this;
    }

    protected function replaceModel(&$stub, $name)
    {
        $model = $this->guessModel($name);
        $modelShortName = class_basename($model);
        $stub = str_replace('{{ model }}', $model, $stub);
        $stub = str_replace('{{ modelShortName }}', $modelShortName, $stub);

        return $this;
    }

    protected function guessModel($name)
    {
        $root = trim($this->rootNamespace(), '\\');

        return Str::replace([$this->getDefaultNamespace($root), 'Repository'], ['App\Models', ''], $name);
    }
}

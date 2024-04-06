<?php

namespace Trovee\Repository\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Trovee\Repository\Attributes\Repository;
use Trovee\Repository\Commands\Generators\EloquentRepositoryMakeCommand;
use Trovee\Repository\Commands\Generators\RepositoryInterfaceMakeCommand;

class RepositoryMakeCommand extends Command
{
    protected $signature = 'make:repository {--model=}';

    protected $description = 'Create a new repository for a model';

    public function handle()
    {
        $model = $this->getTargetModel();

        $this->createOrPassModel($model);

        [$interface, $implementation] = $this->createRepositoryElements($model);

        $this->referRepositoryToModel($model, $interface, $implementation);

        $this->components->info("[{$model} Repository] created successfully");
    }

    protected function getTargetModel()
    {
        $model = $this->option('model');

        if (! $model) {
            $model = $this->ask('What model should the repository be for?');
        }

        return $this->getFullyQualifiedModel($model);
    }

    public function convertPathToNamespace(string $path): string
    {
        return Str::replace('/', '\\', $path);
    }

    protected function getModelNamespace(): string
    {
        return $this->getNamespace('Models');
    }

    protected function getContractNamespace(): string
    {
        return $this->getNamespace('Repositories\Contracts');
    }

    protected function getImplementationNamespace(): string
    {
        return $this->getNamespace('Repositories\Eloquent');
    }

    protected function getFullyQualifiedContract(string $contract): string
    {
        return $this->getContractNamespace().$contract;
    }

    protected function getFullyQualifiedImplementation(string $implementation): string
    {
        return $this->getImplementationNamespace().$implementation;
    }

    protected function removeModelNamespace(string $model): string
    {
        return Str::remove($this->getModelNamespace(), $model);
    }

    protected function getRootNamespace(): string
    {
        return $this->laravel->getNamespace();
    }

    protected function getNamespace(string $namespace): string
    {
        return $this->getRootNamespace().$namespace.'\\';
    }

    protected function getModelAsStub(string $model): string
    {
        $relativePath = Str::remove($this->getRootNamespace(), $model);
        $path = Str::replace('\\', '/', app_path($relativePath).'.php');

        return file_get_contents($path);
    }

    protected function getConfigAsStub(): string
    {
        return file_get_contents(config_path('repository.php'));
    }

    protected function getFullyQualifiedModel(mixed $model)
    {
        return $this->convertPathToNamespace(
            Str::startsWith($model, $this->getModelNamespace())
                ? $model
                : $this->getModelNamespace().$model
        );
    }

    protected function createOrPassModel(string $model)
    {
        if (! class_exists($model)) {
            $this->call('make:model', [
                'name' => $this->removeModelNamespace($model),
                '--factory' => true,
                '--migration' => true,
            ]);
        }
    }

    protected function createRepositoryElements(string $model)
    {
        $base = $this->removeModelNamespace($model);

        $this->call(RepositoryInterfaceMakeCommand::class, [
            'name' => $interface = "{$base}RepositoryContract",
        ]);

        $this->call(EloquentRepositoryMakeCommand::class, [
            'name' => $implementation = "{$base}Repository",
        ]);

        return [$this->getFullyQualifiedContract($interface), $this->getFullyQualifiedImplementation($implementation)];
    }

    protected function referRepositoryToModel(string $model, string $interface, string $implementation)
    {
        $this->components->info('Adding repository attribute to model...');
        $this->addClassAttribute(
            model: $model,
            abstract: $interface,
        );

        $this->components->info('Adding binding configuration...');
        $this->addBindingConfig(
            abstract: $interface,
            concrete: $implementation,
        );
    }

    protected function addClassAttribute(string $model, string $abstract): void
    {
        $content = $this->getModelAsStub($model);

        $className = class_basename($model);
        $useConverter = fn ($use) => "use $use;";

        $shortAbstract = class_basename($abstract);

        $uses = $this->getImports($content)
            ->merge([Repository::class, $abstract])
            ->unique()
            ->map($useConverter)
            ->sort(fn ($a, $b) => Str::length($a) <=> Str::length($b));

        $replacements = [
            '#[Repository({$shortAbstract}::class)]'.PHP_EOL => '',
            $this->getImports($content)->map($useConverter)->implode(PHP_EOL) => $uses->implode(PHP_EOL),
            "class {$className} " => "#[Repository({$shortAbstract}::class)]".PHP_EOL."class {$className} ",
        ];

        $content = Str::replace(array_keys($replacements), array_values($replacements), $content);

        file_put_contents(app_path($this->removeModelNamespace('Models\\'.$model).'.php'), $content);
    }

    protected function getImports(string $content): Collection
    {
        preg_match_all('/use (.*);/', $content, $matches);

        return collect($matches[1])->filter(fn ($use) => $this->isFqcn($use));
    }

    protected function isFqcn(string $class): bool
    {
        return Str::contains($class, '\\');
    }

    protected function addBindingConfig(string $abstract, string $concrete)
    {
        $config = $this->getBindings($content = $this->getConfigAsStub())
            ->put($abstract, $concrete)
            ->unique()
            ->map(fn ($concrete, $abstract) => "\t\t{$abstract}::class => {$concrete}::class,")
            ->values()
            ->toArray();

        usort($config, fn ($a, $b) => Str::startsWith($a, '//') > Str::startsWith($b, '//'));

        $config = implode(PHP_EOL, $config);

        $keyword = "'bindings' => [";

        $content = preg_replace(
            '/'.preg_quote($keyword).'(.*)\]\,/s',
            $keyword.PHP_EOL.$config.PHP_EOL."\t],",
            $content,
            1
        );

        file_put_contents(config_path('repository.php'), $content);
    }

    protected function getBindings(string $content): Collection
    {
        preg_match_all('/\'bindings\' => \[(.*)\]/s', $content, $matches);

        $clear = fn ($binding) => Str::remove(['::class', ','], trim($binding));

        return collect(explode(PHP_EOL, $matches[1][0]))
            ->map(fn ($binding) => explode(' => ', $binding))
            ->reject(fn ($binding) => count($binding) < 2)
            ->mapWithKeys(fn ($binding) => [$clear($binding[0]) => $clear($binding[1])]);
    }
}

<?php

namespace Trovee\Repository\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Trovee\Repository\Attributes\Repository;
use Trovee\Repository\Commands\Generators\EloquentRepositoryMakeCommand;
use Trovee\Repository\Commands\Generators\RepositoryInterfaceMakeCommand;

/** todo: needs refactor */
class RepositoryMakeCommand extends Command
{
    protected $signature = 'make:repository {--model=}';

    protected $description = 'Create a new repository for a model';

    public function handle()
    {
        $model = $this->option('model');

        if (! $model) {
            $model = $this->ask('What model should the repository be for?');
        }

        $target = Str::replace($this->getModelNamespace(), '', $model);

        if (! Str::startsWith($model, $this->getModelNamespace())) {
            $model = $this->getModelNamespace().$model;
        }

        if (! class_exists($model)) {
            $this->call('make:model', [
                'name' => $target,
                '--factory' => true,
                '--migration' => true,
            ]);
        }

        $this->call(RepositoryInterfaceMakeCommand::class, [
            'name' => $interface = "{$target}RepositoryContract",
        ]);

        $this->call(EloquentRepositoryMakeCommand::class, [
            'name' => $implementation = "{$target}Repository",
        ]);

        $this->addClassAttribute(
            class: $model,
            args: [
                'contract' => $this->laravel->getNamespace().'Repositories\Contracts\\'.$interface,
            ]
        );

        $this->addBindingConfig(
            abstract: $interface,
            concrete: $implementation,
        );
    }

    protected function getModelNamespace()
    {
        return $this->laravel->getNamespace().'Models\\';
    }

    private function addClassAttribute(mixed $class, array $args)
    {
        // get class as stub
        $relativePath = Str::replace($this->laravel->getNamespace(), '', $class);
        $path = Str::replace('\\', '/', app_path($relativePath).'.php');
        $content = file_get_contents($path);

        $repository = Repository::class;
        $target = $args['contract'];

        $uses = [
            Model::class,
            Repository::class,
            $target,
        ];

        $replacements = [
            'use Trovee\Repository\Attributes\Repository;'.PHP_EOL.'use App\Repositories\Contracts\PostRepositoryContract;'.PHP_EOL => '',
            '#['.class_basename($repository).'('.class_basename($target).'::class)]'.PHP_EOL => '',
            'class '.class_basename($class).' ' => '#['.class_basename($repository).'('.class_basename($target).'::class)]'
                .PHP_EOL.'class '.class_basename($class).' ',
            'use '.Model::class.';' => implode(PHP_EOL, array_map(fn ($use) => 'use '.$use.';', $uses)),
        ];

        $content = Str::replace(array_keys($replacements), array_values($replacements), $content);

        file_put_contents($path, $content);
    }

    private function addBindingConfig(string $abstract, string $concrete)
    {
        $keyword = "'bindings' => [";
        $path = config_path('repository.php');
        $content = file_get_contents($path);

        $abstract = $this->laravel->getNamespace().'Repositories\Contracts\\'.$abstract.'::class';
        $concrete = $this->laravel->getNamespace().'Repositories\Eloquent\\'.$concrete.'::class';

        $replacements = [
            $keyword => $keyword.PHP_EOL."        {$abstract} => {$concrete},".PHP_EOL,
        ];

        $content = Str::replace(array_keys($replacements), array_values($replacements), $content);

        file_put_contents($path, $content);
    }
}

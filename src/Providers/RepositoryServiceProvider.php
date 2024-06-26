<?php

namespace Trovee\Repository\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Trovee\Repository\Commands;
use Trovee\Repository\Managers\RepositoryManager;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__.'/../../config/repository.php' => config_path('repository.php'),
        ], 'repository-config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/repository.php',
            'repository'
        );

        $this->commands([
            Commands\InstallCommand::class,
            Commands\Generators\RepositoryMakeCommand::class,
            Commands\Generators\CriteriaMakeCommand::class,
            Commands\Generators\HookMakeCommand::class,
        ]);
    }

    public function boot(): void
    {
        $this->handleBindings();

        Model::preventLazyLoading(
            config('repository.should_be_strict', Model::preventsLazyLoading())
        );
        Model::preventAccessingMissingAttributes(
            config('repository.should_be_strict', Model::preventsAccessingMissingAttributes())
        );
        Model::preventSilentlyDiscardingAttributes(
            config('repository.should_be_strict', Model::preventsSilentlyDiscardingAttributes())
        );

    }

    protected function handleBindings(): void
    {
        $this->app->bind('repository.registry', fn ($app) => $app->make(RepositoryManager::class));

        foreach (config('repository.bindings') as $abstract => $concrete) {
            if ($concrete === 'default') {
                continue;
            }
            $this->app->bind($abstract, $concrete);
        }
    }
}

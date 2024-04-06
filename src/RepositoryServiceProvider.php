<?php

namespace Trovee\Repository;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Trovee\Repository\Managers\RegistryManager;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__.'/../config/repository.php' => config_path('repository.php'),
        ], 'repository-config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/repository.php',
            'repository'
        );
        $this->commands([
            Commands\InstallCommand::class,
            Commands\RepositoryMakeCommand::class,
            Commands\CriteriaMakeCommand::class,
        ]);
    }

    public function boot(): void
    {
        //        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
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

    protected function bootManager(): Closure
    {
        return function (Application $app) {
            $manager = $app->make(RegistryManager::class);
            $manager->boot();

            return $manager;
        };
    }

    protected function handleBindings(): void
    {
        $this->app->bind('repository.registry', $this->bootManager());

        foreach (config('repository.bindings') as $abstract => $concrete) {
            if ($concrete === 'default') {
                continue;
            }
            $this->app->bind($abstract, $concrete);
        }
    }
}

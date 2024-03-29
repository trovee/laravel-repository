<?php

namespace Trovee\Repository;

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
    }

    public function boot(): void
    {
//        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->handleBindings();
    }

    protected function bootManager(): \Closure
    {
        return function (Application $app) {
            $manager = new RegistryManager($app);
            $manager->boot();

            return $manager;
        };
    }

    protected function handleBindings(): void
    {
        $this->app->bind('repository.registry', $this->bootManager());

        foreach (config('repository.bindings') as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }

    }

}

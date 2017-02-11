<?php

namespace Deseco\Repositories;

use Deseco\Repositories\Factories\RepositoryFactory;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('deseco.repository', function () {
            return new RepositoryFactory($this->app, $this->app['config']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Handle configuration
     */
    private function handleConfigs()
    {
        $configPath = __DIR__ . '/../config/repositories.php';

        $this->publishes([$configPath => config_path('repositories.php')]);

        $this->mergeConfigFrom($configPath, 'repositories');
    }
}

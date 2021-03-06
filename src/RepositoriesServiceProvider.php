<?php

namespace Deseco\Repositories;

use Deseco\Repositories\Console\GenerateRepositoryCommand;
use Deseco\Repositories\Factories\RepositoryFactory;
use Deseco\Repositories\Libraries\RepositoryConfig;
use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * @var array
     */
    protected $commands = [
        GenerateRepositoryCommand::class,
    ];

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
            return new RepositoryFactory(
                $this->app,
                new RepositoryConfig($this->app)
            );
        });

        $this->commands($this->commands);
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

<?php

namespace Deseco\Repositories\Factories;

use Deseco\Repositories\Exceptions\RepositoryClassNotExistsException;
use Deseco\Repositories\Libraries\RepositoryConfig;
use Illuminate\Container\Container as App;

/**
 * Class RepositoryService
 *
 * @package Deseco\Repositories\Factories
 */
class RepositoryFactory
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * @var \Deseco\Repositories\Libraries\RepositoryConfig
     */
    protected $config;

    /**
     * @var array
     */
    protected $repositories = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $excludedProperties = ['app', 'config', 'name', 'repositories', 'excludedProperties'];

    /**
     * RepositoryService constructor.
     *
     * @param \Illuminate\Container\Container $app
     * @param \Deseco\Repositories\Libraries\RepositoryConfig $config
     */
    public function __construct(App $app, RepositoryConfig $config)
    {
        $this->app = $app;
        $this->config = $config;

        foreach (get_object_vars($this) as $property => $value) {
            if (! in_array($property, $this->excludedProperties)) {
                $this->repositories[$property] = $value;
                unset($this->{$property});
            }
        }
    }

    /**
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        $this->name = $this->repositories[$property];

        return $this->buildRepository();
    }

    /**
     * @return mixed
     * @throws \Deseco\Repositories\Exceptions\RepositoryClassNotExistsException
     */
    private function buildRepository()
    {
        try {
            return $this->app->make($this->buildName());
        } catch (\ReflectionException $e) {
            throw new RepositoryClassNotExistsException($e->getMessage());
        }
    }

    /**
     * @return string
     */
    private function buildName()
    {
        return join('', [
            $this->config->namespace,
            ucfirst($this->name . $this->config->suffix)
        ]);
    }
}

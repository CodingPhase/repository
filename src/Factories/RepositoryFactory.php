<?php

namespace Deseco\Repositories\Factories;

use Deseco\Repositories\Exceptions\RepositoryClassNotExistsException;
use Illuminate\Config\Repository as Config;
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
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $suffix;

    /**
 * @var array
 */
    protected $aliases;

    /**
     * @var string
     */
    protected $name;

    /**
     * RepositoryService constructor.
     *
     * @param \Illuminate\Container\Container $app
     * @param \Illuminate\Config\Repository $config
     */
    public function __construct(App $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;

        $this->namespace = $this->config->get('repositories.namespace');
        $this->suffix = $this->config->get('repositories.suffix');
        $this->aliases = $this->config->get('repositories.aliases');
    }

    /**
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        $this->name = $property;

        return $this->buildRepository();
    }

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $this->name = $method;

        return $this->buildRepository();
    }

    /**
     * @return mixed
     * @throws \Deseco\Repositories\Exceptions\RepositoryClassNotExistsException
     */
    protected function buildRepository()
    {
        if (array_key_exists($this->name, $this->aliases)) {
            $this->name = $this->aliases[$this->name];
        }

        try {
            return $this->app->make($this->buildName());
        } catch (\ReflectionException $e) {
            throw new RepositoryClassNotExistsException($e->getMessage());
        }
    }

    /**
     * @return string
     */
    protected function buildName()
    {
        return $this->namespace . ucfirst($this->name) . $this->suffix;
    }
}

<?php

namespace Deseco\Repositories\Libraries;

use Deseco\Repositories\Exceptions\RepositoryException;
use Illuminate\Container\Container as App;

/**
 * Class RepositoryConfig
 * @package Deseco\Repositories\Libraries
 */
class RepositoryConfig
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * RepositoryConfig constructor.
     *
     * @param \Illuminate\Container\Container $app
     */
    public function __construct(App $app)
    {
        foreach ($app['config']->get('repositories') as $setting => $value) {
            $this->config[$setting] = $value;
        }
    }

    /**
     * @return object
     */
    public function all()
    {
        return (object) $this->config;
    }

    /**
     * @param $value
     *
     * @return mixed
     * @throws \Deseco\Repositories\Exceptions\RepositoryException
     */
    public function get($value)
    {
        if (! array_key_exists($value, $this->config)) {
            throw new RepositoryException('Config value does not exists.');
        }

        return $this->config[$value];
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function __get($value)
    {
        return $this->get($value);
    }
}
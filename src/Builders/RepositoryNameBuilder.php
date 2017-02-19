<?php

namespace Deseco\Repositories\Builders;

/**
 * Class RepositoryNameBuilder
 * @package Deseco\Repositories\Builders
 */
class RepositoryNameBuilder
{
    /**
     * @var
     */
    protected $namespace;

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $factoryMethod;

    /**
     * RepositoryNameBuilder constructor.
     *
     * @param $repositoryPath
     */
    public function __construct($repositoryPath)
    {
        $this->makeNameFromPath($repositoryPath);
        $this->setNamespace();
        $this->setFactoryMethod();
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getFactoryMethod()
    {
        return $this->factoryMethod;
    }

    /**
     * Set namespace
     */
    protected function setNamespace()
    {
        $this->namespace = config('repositories.namespace') . $this->name;
    }

    /**
     * Set factory method
     */
    protected function setFactoryMethod()
    {
        $this->factoryMethod = strtolower(substr($this->name, 0, -strlen(config('repositories.suffix'))));
    }

    /**
     * @param $repositoryPath
     *
     * @return mixed
     */
    protected function makeNameFromPath($repositoryPath)
    {
        $repositoryFilename = explode('/', $repositoryPath);
        list($repositoryClassName) = explode('.', end($repositoryFilename));

        $this->name = $repositoryClassName;
    }
}
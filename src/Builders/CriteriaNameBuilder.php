<?php

namespace Deseco\Repositories\Builders;

/**
 * Class CriteriaNameBuilder
 * @package Deseco\Repositories\Builders
 */
class CriteriaNameBuilder
{
    /**
     * @var string
     */
    protected $suffix = 'Criteria';

    /**
     * @var string
     */
    protected $folder = 'Criteria';

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $namespace;

    /**
     * CriteriaNameBuilder constructor.
     *
     * @param $repositoryClass
     */
    public function __construct($repositoryClass)
    {
        $this->makeCriteriaClassName($repositoryClass);
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
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param $repositoryClass
     */
    protected function makeCriteriaClassName($repositoryClass)
    {
        $repositoryClassParts = explode('\\', $repositoryClass);
        $this->name = end($repositoryClassParts);

        $this->name = $this->createName();
        $this->namespace = $this->createNamespace();
    }

    /**
     * @return string
     */
    protected function createName()
    {
        $name = substr($this->name, 0, -strlen(config('repositories.suffix')));

        return $name . $this->suffix;
    }

    /**
     * @return string
     */
    protected function createNamespace()
    {
        return config('repositories.namespace') . $this->folder . '\\' . $this->name;
    }
}

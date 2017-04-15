<?php

namespace Deseco\Repositories\Generators;

use Deseco\Repositories\Libraries\FactoryMethodNameBuilder;
use Deseco\Repositories\Libraries\RepositoryConfig;
use Illuminate\Filesystem\Filesystem;

/**
 * Class RepositoryGenerator
 * @package Deseco\Repositories\Generators
 */
class RepositoryGenerator
{
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Illuminate\Console\Command
     */
    protected $command;

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $alias;

    /**
     * @var array
     */
    protected $headers = ['Class', 'Repository', 'Property', 'Status'];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Generator constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param \Deseco\Repositories\Libraries\RepositoryConfig $config
     */
    public function __construct(Filesystem $filesystem, RepositoryConfig $config)
    {
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->stubs = (object) [
            'repositories' => $filesystem->get(__DIR__ . '/../../stubs/repositories.stub'),
            'repository' => $filesystem->get(__DIR__ . '/../../stubs/repository.stub'),
        ];
    }

    /**
     * @return void
     */
    public function make()
    {
        $this->command->info('Generating repository...');
        $this->command->info('');

        $this->createRepositoryClass();
        $this->addData($this->getRepositoryName());

        if (! $this->isRepositoriesClassExists()) {
            $this->createRepositoriesClass();
            $this->addData($this->getRepositoriesName(), $this->getRepositoryName(), $this->getAliasName());
        } else {
            $this->updateRepositoriesClass();
            $this->addData($this->getRepositoriesName(), $this->getRepositoryName(), $this->getAliasName(), 'Updated');
        }

        $this->command->table($this->headers, $this->data);
        $this->command->info('');
        $this->command->info('Done!');
    }

    /**
     * @param \Illuminate\Console\Command $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
       $this->name = $name;
    }

    /**
     * @param $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return void
     */
    protected function createRepositoryClass()
    {
        $this->filesystem->put(
            $this->getRepositoryFilePath(),
            str_replace(
                ['{namespace}', '{class}'],
                [$this->getRepositoriesNamespace(), $this->getRepositoryName()],
                $this->stubs->repository
            )
        );
    }

    /**
     * @return void
     */
    protected function createRepositoriesClass()
    {
        $this->filesystem->put(
            $this->getRepositoriesFilePath(),
            str_replace(
                ['{namespace}', '{class}', '{output}'],
                [$this->getRepositoriesNamespace(), $this->getRepositoriesName(), $this->generateProperty()],
                $this->stubs->repositories
            )
        );

    }

    /**
     * return @void
     */
    protected function updateRepositoriesClass()
    {
        $content = $this->filesystem->get($this->getRepositoriesFilePath());

        $this->filesystem->put(
            $this->getRepositoriesFilePath(),
            $this->generateProperty(substr($content, 0, strrpos(trim($content), "\n")))
        );
    }

    /**
     * @return string
     */
    protected function getRepositoryFilePath()
    {
        return $this->config->path . $this->getRepositoryName() . '.php';
    }

    /**
     * @return string
     */
    protected function getRepositoriesFilePath()
    {
        return $this->config->path . $this->config->class . '.php';
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return $this->name . $this->config->suffix;
    }

    /**
     * @return mixed
     */
    protected function getRepositoriesName()
    {
        return $this->config->class;
    }

    /**
     * @return string
     */
    protected function getRepositoriesNamespace()
    {
        return substr($this->config->namespace, 0, -1);
    }

    /**
     * @return string
     */
    protected function getPropertyName()
    {
        return lcfirst($this->name);
    }

    /**
     * @return mixed
     */
    protected function getAliasName()
    {
        return $this->alias;
    }

    /**
     * @param bool $content
     *
     * @return string
     */
    protected function generateProperty($content = false)
    {
        $repositoryClass = $this->getRepositoryName();
        $propertyName = $this->getAliasName();
        $propertyValue = $this->getPropertyName();

        if ($content) {
            return $content . "\t\n\n\t/**\n\t * @var {$repositoryClass}\n\t */\n\tpublic \${$propertyName} = '{$propertyValue}';\n}\n";
        }

        return "/**\n\t * @var {$repositoryClass}\n\t */\n\tpublic \${$propertyName} = '{$propertyValue}';";
    }

    /**
     * @return bool
     */
    protected function isRepositoriesClassExists()
    {
        return $this->filesystem->exists($this->getRepositoriesFilePath());
    }

    /**
     * @param $repository
     * @param string $property
     * @param string $alias
     * @param string $status
     */
    protected function addData($repository, $property = '-', $alias = '-', $status = 'Created')
    {
        $this->data[] = [
            'repository' => $repository,
            'property' => $property,
            'alias' => $alias,
            'status' => $status
        ];
    }
}

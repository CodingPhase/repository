<?php

namespace Deseco\Repositories\Hints;

use Deseco\Repositories\Builders\FactoryMethodNameBuilder;
use Deseco\Repositories\Builders\RepositoryNameBuilder;
use Illuminate\Filesystem\Filesystem;

/**
 * Class Generator
 * @package Deseco\Repositories\Hints
 */
class Generator
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
     * Generator constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        foreach (config('repositories') as $setting => $value) {
            $this->config[$setting] = $value;
        }
    }

    /**
     * @param $filename
     */
    public function make($filename)
    {
        $this->command->info('Started generating hints...');
        $this->command->info('');

        $this->deleteFileIfExists($filename);

        $file = $this->filesystem->get(__DIR__ . '/../../stubs/class.stub');

        $data = [
            '{facade}' => $this->config['facade'],
            '{output}' => $this->generateMethods(),
        ];

        foreach ($data as $string => $value) {
            $file = str_replace($string, $value, $file);
        }

        $this->filesystem->put($filename, $file);
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
     * @param $filename
     */
    protected function deleteFileIfExists($filename)
    {
        if ($this->filesystem->exists($filename)) {
            $this->filesystem->delete($filename);
        }
    }

    /**
     * @return array
     */
    protected function getMethods()
    {
        $methods = [];

        foreach ($this->globRepositories() as $repositoryPath) {
            $repositoryNameBuilder = new RepositoryNameBuilder($repositoryPath);
            $methods[$repositoryNameBuilder->getFactoryMethod()] = $repositoryNameBuilder->getNamespace();
            $this->command->info("Created method {$repositoryNameBuilder->getFactoryMethod()} for repository {$repositoryNameBuilder->getNamespace()}");
        }

        foreach ($this->config['aliases'] as $method => $repository) {
            $namespace = $this->config['namespace'] . $repository . $this->config['suffix'];
            $methods[$method] = $namespace;
            $this->command->info("Created method {$method} for repository {$namespace}");
        }

        return $methods;
    }

    /**
     * @return array
     */
    protected function globRepositories()
    {
        $namespaceParts = explode('\\', $this->config['namespace']);
        array_shift($namespaceParts);

        return $this->filesystem->files(app_path(implode(DIRECTORY_SEPARATOR, array_filter($namespaceParts))));
    }

    /**
     * @return string
     */
    protected function generateMethods()
    {
        $output = '';
        $counter = 0;

        foreach ($this->getMethods() as $method => $value) {
            if ($counter !== 0) {
                $output .= "\n\n\t";
            }

            $output .= "/**\n\t * @return {$value}\n\t */\n\tpublic static function {$method}() { return new {$value}; }";
            $counter++;
        }

        return $output;
    }
}

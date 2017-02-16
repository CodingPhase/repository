<?php

namespace Deseco\Repositories\Hints;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;

class Generator
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Generator constructor.
     *
     * @param \Illuminate\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Config $config, Filesystem $files)
    {
        $this->files = $files;

        $this->config = [
            'namespace' => $config->get('repositories.namespace'),
            'facade' => $config->get('repositories.facade'),
            'suffix' => $config->get('repositories.suffix'),
            'path' => $config->get('repositories.path'),
        ];
    }

    /**
     * @param $filename
     */
    public function make($filename)
    {
        if ($this->files->exists($filename)) {
            $this->files->delete($filename);
        }

        $file = $this->files->get(__DIR__ . '/../../stubs/class.stub');

        $data = [
            '{facade}' => $this->config['facade'],
            '{output}' => $this->generateMethods(),
        ];

        foreach ($data as $string => $value) {
            $file = str_replace($string, $value, $file);
        }

        $this->files->put($filename, $file);
    }

    /**
     * @return array
     */
    protected function getMethods()
    {
        $methods = [];

        foreach ($this->files->files(app_path($this->config['path'])) as $file) {
            $class = $this->getClassNameFromPath($file);
            $methods[$this->getMethodNameFromClass($class)] = $this->config['namespace'] . $class;
        }

        return $methods;
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

    /**
     * @param $path
     *
     * @return mixed
     */
    protected function getClassNameFromPath($path)
    {
        $fullPathToFile = explode('/', $path);
        $explodedFileName = explode('.', end($fullPathToFile));

        return $explodedFileName[0];
    }

    /**
     * @param $class
     *
     * @return string
     */
    protected function getMethodNameFromClass($class)
    {
        $method = substr($class, 0, -strlen($this->config['suffix']));

        return strtolower($method);
    }
}
<?php

namespace Deseco\Repositories\Console;

use Deseco\Repositories\Generators\RepositoryGenerator;
use Illuminate\Console\Command;

/**
 * Class GenerateCommand
 * @package Deseco\Repositories\Console
 */
class GenerateRepositoryCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'repository:make';

    /**
     * @var string
     */
    protected $description = 'Generate repository';

    /**
     * @var \Deseco\Repositories\Generators\RepositoryGenerator
     */
    protected $generator;

    /**
     * GenerateCommand constructor.
     *
     * @param \Deseco\Repositories\Generators\RepositoryGenerator $generator
     */
    public function __construct(RepositoryGenerator $generator)
    {
        parent::__construct();

        $this->generator = $generator;
    }

    /**
     * Generate repository hints
     */
    public function handle()
    {
        $name = $this->ask('Enter repository name:');
        $alias = $this->ask('Enter alias name', lcfirst($name));
        $this->generator->setCommand($this);
        $this->generator->setName($name);
        $this->generator->setAlias($alias);
        $this->generator->make();
    }
}

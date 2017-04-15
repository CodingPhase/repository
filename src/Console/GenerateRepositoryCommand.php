<?php

namespace Deseco\Repositories\Console;

use Deseco\Repositories\Hints\Generator;
use Illuminate\Console\Command;

/**
 * Class GenerateCommand
 * @package Deseco\Repositories\Console
 */
class GenerateCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'repository:hints';

    /**
     * @var string
     */
    protected $description = 'Generate facade hints for repositories';

    /**
     * @var \Deseco\Repositories\Hints\Generator
     */
    protected $generator;

    /**
     * @var string
     */
    protected $filename = '_repo_hints.php';

    /**
     * GenerateCommand constructor.
     *
     * @param \Deseco\Repositories\Hints\Generator $generator
     */
    public function __construct(Generator $generator)
    {
        parent::__construct();

        $this->generator = $generator;
    }

    /**
     * Generate repository hints
     */
    public function handle()
    {
        $this->generator->setCommand($this);
        $this->generator->make(base_path($this->filename));
    }
}

<?php

namespace Deseco\Repositories\Console;

use Deseco\Repositories\Hints\Generator;
use Illuminate\Console\Command;

class GenerateCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'repo:hints';

    /**
     * @var string
     */
    protected $description = 'Generate hints for repositories';

    /**
     * @var \Deseco\Repositories\Hints\Generator
     */
    private $generator;

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
     *
     */
    public function handle()
    {
        $this->generator->make(base_path('_repo_hints.php'));
    }
}

<?php

namespace Deseco\Repositories\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class RepositoryFacade
 * @package Deseco\Repositories\Facade
 */
class RepositoryFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'deseco.repository';
    }
}

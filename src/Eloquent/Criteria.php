<?php

namespace Deseco\Repositories\Eloquent;

use Deseco\Repositories\Contracts\CriteriaInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Criteria
 * @package Deseco\Repositories\Eloquent
 */
abstract class Criteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Criteria constructor.
     *
     * @param $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }
}

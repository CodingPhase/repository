<?php

namespace Deseco\Repositories\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilters
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var
     */
    protected $builder;

    /**
     * Create a new QueryFilters instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            $name = camel_case($name);

            if (! method_exists($this, $name)) {
                continue;
            }

            if (is_array($value) && count($value)) {
                $this->$name($value);
            } else if (strlen($value)) {
                $this->$name($value);
            }
        }

        $this->builder = $this->orderBy();

        return $this->builder;
    }

    /**
     * @return array
     */
    public function filters()
    {
        return $this->request->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orderBy()
    {
        return $this->builder;
    }
}

<?php

namespace Deseco\Repositories\Traits\Eloquent;

use Deseco\Repositories\Filters\QueryFilters;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filterable
 * @package Deseco\Repositories\Traits\Eloquent
 */
trait Filterable
{
    /**
     * Filter results
     *
     * @param Builder $builder
     * @param QueryFilters $filters
     * @return Builder
     */
    public function scopeFilter(Builder $builder, QueryFilters $filters)
    {
        return $filters->apply($builder);
    }
}

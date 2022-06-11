<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

trait FilterTrait
{
    /**
     * @method filter
     *
     * @param Builder $builder
     * @param QueryFilter $filter
     * @return Builder
     */
    public function scopeFilter(Builder $builder, QueryFilter $filter): Builder
    {
        return $filter->apply($builder);
    }
}
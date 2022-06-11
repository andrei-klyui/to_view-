<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

class CategoryFilter extends QueryFilter
{
    /**
     * Filter by name
     *
     * @param  string  $name
     * @return Builder
     */
    public function name(string $name): Builder
    {
        if ($name)  {
            $this->whereLike('categories.name', $name);
        }

        return $this->builder;
    }
}

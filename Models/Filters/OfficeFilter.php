<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

class OfficeFilter extends QueryFilter
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
            $this->whereLike('offices.name', $name);
        }

        return $this->builder;
    }
}

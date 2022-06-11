<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

class RoleFilter extends QueryFilter
{
    /**
     * Filter by role
     *
     * @param  string  $name
     * @return Builder
     */
    public function name(string $name): Builder
    {
        if ($name)  {
            $this->whereLike('roles.name', $name);
        }

        return $this->builder;
    }
}

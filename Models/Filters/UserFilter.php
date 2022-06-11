<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilter extends QueryFilter
{
    /**
     * Filter by roles
     *
     * @param array $roles
     * @return Builder
     */
    public function roles(array $roles): Builder
    {
        if ($roles) {
            $this->builder->whereHas('roles', function ($query) use ($roles) {
                return $query->whereIn('name', $roles);
            });
        }

        return $this->builder;
    }

    /**
     * Filter by name
     *
     * @param string $name
     * @return Builder
     */
    public function name(string $name): Builder
    {
        if ($name) {
            $this->whereLike('users.name', $name);
        }

        return $this->builder;
    }

    /**
     * Filter by email
     *
     * @param string $email
     * @return Builder
     */
    public function email(string $email): Builder
    {
        if ($email) {
            $this->whereLike('users.email', $email);
        }

        return $this->builder;
    }
}

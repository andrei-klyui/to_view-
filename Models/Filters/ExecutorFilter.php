<?php

declare(strict_types=1);

namespace App\Models\Filters;


use Illuminate\Database\Eloquent\Builder;

class ExecutorFilter extends QueryFilter
{
    /**
     * Filter by name
     *
     * @param string $name
     * @return Builder
     */
    public function name(string $name): Builder
    {
        if ($name) {
            $this->whereLike('executors.name', $name);
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
            $this->whereLike('executors.email', $email);
        }

        return $this->builder;
    }
}

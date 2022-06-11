<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

class PostFilter extends QueryFilter
{
    /**
     * Filter by title
     *
     * @param  string  $title
     * @return Builder
     */
    public function title(string $title): Builder
    {
        if ($title)  {
            $this->whereLike('posts.title', $title);
        }

        return $this->builder;
    }

    /**
     * Filter by description
     *
     * @param  string  $description
     * @return Builder
     */
    public function description(string $description): Builder
    {
        if ($description)  {
            $this->whereLike('posts.description', $description);
        }

        return $this->builder;
    }
}
